moment.locale('fr');

var default_note = {
  id: null,
  title: '',
  description: '',
  date_due: '',
  color_id: 'yellow'
};

var colors = [
  { id: 'yellow', name: 'Jaune'},
  { id: 'blue', name: 'Bleu'},
  { id: 'green', name: 'Vert'},
  { id: 'purple', name: 'Violet'},
  { id: 'red', name: 'Rouge'},
  { id: 'orange', name: 'Orange'},
  { id: 'grey', name: 'Gris'},
  { id: 'brown', name: 'Marron'},
  { id: 'deep_orange', name: 'Orange foncé'},
  { id: 'dark_grey', name: 'Gris foncé'},
  { id: 'pink', name: 'Rose'},
  { id: 'teal', name: 'Turquoise'},
  { id: 'cyan', name: 'Bleu intense'},
  { id: 'lime', name: 'Vert citron'},
  { id: 'light_green', name: 'Vert clair'},
  { id: 'amber', name: 'Ambre'},
];

var Home = Vue.extend({
  template: '#home',
  props: ['loading', 'query'],
  data: function() { return {
    notes: []
  }},
  components: {
    'due': Vue.extend({
      template: '#due',
      props: ['note']
    }),
    'actions': Vue.extend({
      template: '#actions',
      props: ['note', 'remove']
    })
  },
  computed: {
    filteredNotes: function() {
      return this.$options.filters.filterBy(this.notes, this.query);
    }
  },
  methods: {
    remove: function (note) {
      if (!confirm('Etes-vous sûr ?')) {
        return false;
      }
      
      this.loading = true;

      localforage.removeItem(note.id).then(function() {
        // TODO
      }).catch(function(err) {
        // TODO
        console.log(err);
      });
      
      this.$http.delete('/notes/'+note.id).then(function(response) {
        this.notes.$remove(note);
        this.loading = false;
      }, function(response) {
        this.loading = false;
        alert(response.data.data.message);
      });
    }
  },
  ready: function() {
    this.loading = true;
    
    this.$http.get('/notes').then(function(response) {
      /*for (var i = 0; i < response.data.data.notes.length; i++) {
        localforage.setItem(response.data.data.notes[i].id, response.data.data.notes[i]).then(function (val) {
          // TODO
        }).catch(function(err) {
          // TODO
          console.log(err);
        });
      }*/
      
      this.notes = response.data.data.notes;
      this.loading = false;
    }, function(response) {
      this.loading = false;
      alert(response.data.data.message);
    });
  },
  filters: {
    marked: marked
  }
});

var NoteForm = Vue.extend({
  template: '#note_form',
  props: ['loading'],
  data: function() { return {
    colors: colors,
    note: default_note
  }},
  components: {
    'datepicker': VueStrap.datepicker
  },
  ready: function() {
    this.note = default_note;
    
    if (this.$route.params.id) {
      this.loading = true;
      
      this.$http.get('/notes/'+this.$route.params.id).then(function(response) {
        /*localforage.getItem(this.$route.params.id).then(function(val) {
          // TODO
        }).catch(function(err) {
          // TODO
          console.log(err);
        });*/
        
        this.note = response.data.data.note;
        this.loading = false;
      }, function(response) {
        this.loading = false;
        alert(response.data.data.message);
      });
    }
  },
  methods: {
    cancel: function() {
      this.$router.go({name: 'home' });
    },
    create: function() {
      this.loading = true;
      
      this.$http.post('/notes', this.note).then(function(response) {
        localforage.setItem(response.data.data.note.id, response.data.data.note).then(function(val) {
          // TODO
        }).catch(function(err) {
          // TODO
          console.log(err);
        });
        
        this.note = default_note;
        this.loading = false;
        this.$router.go({name: 'home' });
      }, function(response) {
        this.loading = false;
        alert(response.data.data.message);
      });
    },
    update: function() {
      this.loading = true;
      
      localforage.setItem(this.note.id, this.note).then(function(val) {
        // TODO
      }).catch(function(err) {
        // TODO
        console.log(err);
      });

      this.$http.put('/notes/'+this.note.id, this.note).then(function(response) {        
        this.note = default_note;
        this.loading = false;
        this.$router.go({name: 'home' });
      }, function(response) {
        this.loading = false;
        alert(response.data.data.message);
      });
    },
    createOrUpdate: function() {
      if (this.note.id == null) {
        this.create();
      } else {
        this.update();
      }
    }
  }
});

var App = Vue.extend({
  data: {
    loading: false,
    query: ''
  }
});

var Router = new VueRouter();

Router.map({
  '/': {
    component: Home,
    name: 'home'
  },
  '/create': {
    component: NoteForm,
    name: 'create'
  },
  '/edit/:id': {
    component: NoteForm,
    name: 'edit'
  }
});

Router.start(App, '#app');