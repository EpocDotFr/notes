<?php
// Routes

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

function clean_note_data($note, $allowed_columns) {
  if (!empty($note['date_due'])) {
    $note['date_due'] = date_create()->setTimestamp($note['date_due'])->format('Y-m-d');
  } else {
    $note['date_due'] = null;
  }
  
  $note['id'] = (int) $note['id'];

  $note = array_intersect_key($note, array_flip($allowed_columns));
  
  return $note;
}

// GUI
$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
  return $this->renderer->render($response, 'index.php', $args);
});

// Notes list
$app->get('/notes', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
  $kanboard_settings = $this->settings['kanboard'];

  $notes = $this->kanboardapi->getAllTasks($kanboard_settings['project_id'], 1);
  
  if ($notes === false) {
    throw new Exception('Error getting notes', 502);
  }
  
  $notes = array_map(function($note) use($kanboard_settings) {
    return clean_note_data($note, $kanboard_settings['allowed_columns']);
  }, array_filter($notes, function($note) use($kanboard_settings) {
    return $note['column_id'] == $kanboard_settings['column_id'] and $note['swimlane_id'] == $kanboard_settings['swimlane_id'];
  }));
  
  return $response->withJson([
    'result' => 'success',
    'data' => [
      'notes' => array_values($notes)
    ]
  ]);
});

// Note creation
$app->post('/notes', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
  $kanboard_settings = $this->settings['kanboard'];
  
  // title, description, color_id, date_due
  $note = $request->getParsedBody();
  
  $note['project_id'] = $kanboard_settings['project_id'];
  $note['column_id'] = $kanboard_settings['column_id'];
  $note['swimlane_id'] = $kanboard_settings['swimlane_id'];
  
  $note_id = $this->kanboardapi->createTask($note);
  
  if ($note_id === false) {
    throw new Exception('Error creating note', 502);
  }
  
  $note['id'] = $note_id;
  
  unset($note['project_id'], $note['column_id'], $note['swimlane_id']);
      
  return $response->withJson([
    'result' => 'success',
    'data' => [
      'note' => $note
    ]
  ]);
});

// Getting a note
$app->get('/notes/{id}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
  $kanboard_settings = $this->settings['kanboard'];
  
  $note = $this->kanboardapi->getTask($args['id']);

  if ($note === false) {
    throw new Exception('Error getting note', 502);
  }
  
  $note = clean_note_data($note, $kanboard_settings['allowed_columns']);
  
  return $response->withJson([
    'result' => 'success',
    'data' => [
      'note' => $note
    ]
  ]);
});

// Deleting a note
$app->delete('/notes/{id}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
  $kanboard_settings = $this->settings['kanboard'];
  
  $result = $this->kanboardapi->removeTask($args['id']);

  if ($result === false) {
    throw new Exception('Error deleting note', 502);
  }
  
  return $response->withJson([
    'result' => 'success',
    'data' => []
  ]);
});

// Updating a note
$app->put('/notes/{id}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
  $kanboard_settings = $this->settings['kanboard'];
  
  // title, description, color_id, date_due
  $note = $request->getParsedBody();
  
  $note['id'] = $args['id'];

  $result = $this->kanboardapi->updateTask($note);
  
  if ($result == false) {
    throw new Exception('Error updating note', 502);
  }
  
  return $response->withJson([
    'result' => 'success',
    'data' => [
      'note' => $note
    ]
  ]);
});
