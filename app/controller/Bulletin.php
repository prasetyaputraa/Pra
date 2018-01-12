<?php

require('./lib/system/database/Mysql.php');
require('./lib/system/core/Model.php');
require('./lib/Validator.php');
require('./lib/Paginator.php');
require('./lib/Uploader.php');
require('./app/model/Comment_Model.php');

class Bulletin extends Controller
{
  private $commentModel  = null;
  private $imageUploader = null;
  private $validator     = null;
  private $limit         = 10;

  private $mimesBinary = array(
    'image/jpeg',
    'image/jpg',
    'image/png',
    'image/gif'
  );

  public function __construct() 
  {
    $this->imageUploader = new Uploader(ROOT . DIRECTORY_SEPARATOR . UPLOADS_IMAGE_PATH . 'comments' . DIRECTORY_SEPARATOR);
    $this->commentModel  = new Comment_Model();

    $this->imageUploader->replaceErrorMessage('checkMimeBinaries', 'File type must be jpg, jpeg, png or gif');
    $this->imageUploader->setRestriction(
      100, 
      1000000, 
      $this->mimesBinary
    );
  }

  public function show() 
  {
    $content = $this->getComments();

    $this->render('bulletin', array(
      'comments'  => $content['comments'],
      'paginator' => $content['paginator']
    ));
  }

  public function action()
  {
    switch (get_input_value('action')) {
      case 'update':
        $this->update();
        break;
      case 'delete':
        $this->delete();
        break;
      default:
        $this->renderError('400', 'Bad Request', get_input_value('page'));
    }
  }

  public function insert() 
  {
    $this->validator = new Validator();

    $errors = array();
    $image  = $this->imageUploader->upload(get_file('image'));

    if (!$image) {
      $errors = $this->imageUploader->getErrors();
    }

    $data = array(
      'title'    => get_input_value('title'),
      'comment'  => get_input_value('comment'),
      'image'    => (!empty($image)) ? $image : null,
      'password' => get_input_value('password')
    );

    $this->validator->setParam(array(
      array('empty', $data['title'], 'Title'), 
      array('length', $data['title'], 'Title', 10, 32), 
      array('empty', $data['comment'], 'Comment'), 
      array('length', $data['comment'], 'Comment', 10, 200), 
      array('length', $data['password'], 'Password', 4, 4),
      array('digit', $data['password'], 'Password')
    ));

    $this->validator->validate();

    $errors = array_merge($errors, $this->validator->getErrors());

    if (empty($errors)) {
      $this->commentModel->save(array(
        'title'    => $data['title'],
        'comment'  => $data['comment'], 
        'image'    => $data['image'],
        'password' => $data['password']
      ));
      
      header('Location: index.php');
      exit;
    }

    $content = $this->getComments();

    $this->render('bulletin', array(
      'comments'  => $content['comments'],
      'paginator' => $content['paginator'],
      'errors'    => $errors
    ));
  }

  private function update()
  {
    $id   = get_input_value('id');
    $page = get_input_value('page');

    if (empty($comments = $this->commentModel->fetch(null, "id = " . $id))) {
      $this->renderError('404', 'Comment not found', $page);
    }

    $errors  = array();
    $comment = $comments[0];

    $passwordMatch = true;

    if (empty($comment['password'])) {
      $errors[]      = "This message can't be updated, because this message has no password been set.";
      $passwordMatch = false;
    } elseif (get_input_value('password') !== $comment['password']) {
      $errors[]      = "The password you entered do not match. Please try again.";
      $passwordMatch = false;
    } 

    if (get_input_value('update_confirm') && empty($errors)) {
      $data = array(
        'title'   => get_input_value('title'),
        'comment' => get_input_value('comment')
      );

      $comment['title']   = $data['title'];
      $comment['comment'] = $data['comment'];

      $deleteImage = get_input_value('delete_image');

      $this->validator = new validator();

      $image = $this->imageUploader->upload(get_file('image'));

      if (!$image) {
        $errors = $this->imageUploader->getErrors();
      }

      $data['image'] = ($deleteImage || !$image) ? null : $image;

      $this->validator->setParam(array(
        array('empty', $data['title'], 'Title'), 
        array('length', $data['title'], 'Title', 10, 32), 
        array('empty', $data['comment'], 'Comment'), 
        array('length', $data['comment'], 'Comment', 10, 200), 
      ));

      $this->validator->validate();

      $errors = array_merge($errors, $this->validator->getErrors());

      if (empty($errors)) {
        if ($deleteImage) {
          if (!$this->imageUploader->deleteFile($comment['image'])) {
            $notif = "Error deleting image file";
          }
        }

        $this->commentModel->update($data, 'id = ' . $id);

        header('Location: index.php?page=' . $page);
        exit;
      }
    }

    $this->render('update_comment', array(
      'passwordMatch' => $passwordMatch, 
      'page'          => $page,
      'comment'       => $comment,
      'errors'        => $errors
    ));
  }

  private function delete()
  {
    $id   = get_input_value('id');
    $page = get_input_value('page');

    if (empty($comments = $this->commentModel->fetch(null, "id = " . $id))) {
      $this->renderError('404', 'Comment not found', $page);
    }

    $errors  = array();
    $comment = $comments[0];

    if (empty($comment['password'])) {
      $errors[] = "This message can't delete, because this message has not been set password.";
    } elseif (get_input_value('password') !== $comment['password']) {
      $errors[] = "The password you entered do not match. Please try again.";
    }

    if (get_input_value('delete_confirm') && empty($errors)) {
      if (!$this->imageUploader->deleteFile($comment['image'])) {
        $notif = "Error deleting image file";
      }

      $this->commentModel->delete('id = ' . $id);

      header('Location: index.php?page=' . $page);
      exit;
    }

    $this->render('delete_comment', array(
      'page'    => $page,
      'comment' => $comment,
      'errors'  => $errors
    ));
  }

  private function getComments()
  {
    $paginator = new Paginator(
      $this->commentModel->count('*'),
      $this->limit,
      get_input_value('page'),
      5
    );

    $comments = $this->commentModel->fetch(null, null, 'time_sent desc', $this->limit, $paginator->getOffset());

    return array('comments' => $comments, 'paginator' => $paginator);
  }

  private function renderError($errorCode, $message, $page)
  {
    output_http_status($errorCode);

    $this->render('error_response', array(
      'code'    => $errorCode,
      'message' => $message,
      'link'    => 'index.php?page=' . $page
    ));

    exit;
  }
}
