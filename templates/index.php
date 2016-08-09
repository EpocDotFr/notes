<!DOCTYPE html>
<html lang="fr-FR" manifest="/manifest.appcache">
<head>
  <meta charset="UTF-8">

  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimal-ui">
  <meta name="robots" content="noindex, nofollow" />
  
  <meta name="apple-mobile-web-app-capable" content="yes">
  
  <link rel="apple-touch-icon" sizes="57x57" href="/img/apple-icon-57x57.png" />
  <link rel="apple-touch-icon" sizes="72x72" href="/img/apple-icon-72x72.png" />
  <link rel="apple-touch-icon" sizes="114x114" href="/img/apple-icon-114x114.png" />
  <link rel="apple-touch-icon" sizes="144x144" href="/img/apple-icon-144x144.png" />
  
  <link rel="icon" type="image/png" href="/favicon.png" />
  <!--[if IE]><link rel="shortcut icon" href="/favicon.ico" /><![endif]-->

  <link rel="icon" href="/img/android-icon-192x192.png" sizes="192x192">
  <link rel="icon" href="/img/android-icon-128x128.png" sizes="128x128">

  <title>Notes</title>

  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <link rel="stylesheet" href="/css/font-awesome.min.css">
  <link rel="stylesheet" href="/css/app.css">
</head>
<body>
  <div id="app">
    <div class="loading" v-show="loading">
      <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
    </div>

    <nav class="container-fluid">
      <div class="pull-left">
        <ul class="nav nav-pills">
          <li v-link-active><a v-link="{ name: 'home', exact: true, activeClass: 'active' }">Tableau</a></li>
          <li v-link-active><a v-link="{ name: 'create', exact: true, activeClass: 'active' }">Créer</a></li>
        </ul>
      </div>
      <div class="pull-right">
        <input type="search" class="form-control" placeholder="Filtrer..." v-model="query" size="15">
      </div>
      <div class="clearfix"></div>
    </nav>
    <hr>
    <router-view :loading.sync="loading" :query="query"></router-view>
  </div>
  
  <template id="note_form">
    <div class="container">
      <form class="form-horizontal" @submit.prevent="createOrUpdate">
        <div class="form-group">
          <label class="control-label col-xs-12 col-sm-3">Titre</label>
          <div class="col-xs-12 col-sm-9">
            <input type="text" class="form-control" required v-model="note.title" :disabled="loading">
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-xs-12 col-sm-3">Description</label>
          <div class="col-xs-12 col-sm-9">
            <textarea class="form-control" v-model="note.description" :disabled="loading" rows="8"></textarea>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-xs-12 col-sm-3">Couleur</label>
          <div class="col-xs-12 col-sm-9">
            <div v-for="color in colors" class="color-square color-{{ color.id }}" :class="{'selected': note.color_id == color.id}" @click="note.color_id = color.id"></div>
            <input type="hidden" v-model="note.color_id">
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-xs-12 col-sm-3">Rappel</label>
          <div class="col-xs-12 col-sm-9">
            <datepicker :value.sync="note.date_due" format="yyyy-MM-dd"></datepicker>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-9">
            <button type="button" @click="cancel()" class="btn btn-default" :disabled="loading">Annuler</button>
            <button type="submit" class="btn btn-primary" :disabled="loading">Enregistrer</button>
          </div>
        </div>
      </form>
    </div>
  </template>
  
  <template id="due">
    <span v-if="note.date_due" class="label label-default pull-right">
      <i class="fa fa-clock-o"></i> {{ note.date_due | moment "calendar" }}
    </span>
  </template>
  
  <template id="actions">
    <div class="pull-right">
      <button type="button" class="btn btn-default btn-sm" v-link="{ name: 'edit', params: { id: note.id }}" title="Modifier"><i class="fa fa-pencil"></i></button>
      <button type="button" class="btn btn-default btn-sm" @click="remove(note)" title="Supprimer"><i class="fa fa-trash"></i></button>
      <a class="btn btn-default btn-sm" :href="note.url" title="Voir sur Kanboard" target="_blank"><i class="fa fa-link"></i></a>
    </div>
  </template>
  
  <template id="home">
    <div class="container-fluid">
      <div class="row">
        <div class="col-xs-12 text-muted lead text-center" v-show="filteredNotes.length == 0 && !loading">Aucune note à afficher.</div>
        <div class="col-xs-6 col-sm-2" v-for="note in filteredNotes">
          <section class="panel panel-default panel-note color-{{ note.color_id }}">
            <header class="panel-heading" v-if="note.title && note.description">
              <due :note="note"></due>
              {{ note.title }}
            </header>
            <article class="panel-body" v-if="note.title && !note.description">
              <due :note="note"></due>
              {{ note.title }}
              <actions :note="note" :remove="remove"></actions>
            </article>
            <article class="panel-body" v-if="note.description">
              {{{ note.description | marked }}}
              <actions :note="note" :remove="remove"></actions>
            </article>
          </section>
        </div>
      </div>
    </div>
  </template>
  
  <script src="/js/moment.min.js"></script>
  <script src="/js/moment.fr.js"></script>
  <script src="/js/marked.min.js"></script>
  <script src="/js/vue.min.js"></script>
  <script src="/js/vue-router.min.js"></script>
  <script src="/js/vue-resource.min.js"></script>
  <script src="/js/vue-moment.min.js"></script>
  <script src="/js/vue-strap.min.js"></script>
  <script src="/js/localforage.min.js"></script>
  <script src="/js/app.js"></script>
</body>
</html>