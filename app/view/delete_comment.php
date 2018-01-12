<html>
  <head>
    <title>Challenge30 - Level 6</title>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
  </head>
  <body>
    <div id="container">
    <h2>Delete this comment?</h2>
      <div id="form-section">
        <?php if (!empty($errors)) : ?>
          <p id="notification-text">
            <?php foreach ($errors as $errorMessage) : ?>
              <?php echo $errorMessage ?>
              <br>
            <?php endforeach ?>
          </p>
        <?php endif ?>
      </div>
      <div id="comment-section">
        <div class="comment-individual">
          <div class="comment-individual-titlebox"><?php echo h($comment['title']) ?></div>
          <div class="comment-individual-commentbox"><?php echo nl2br(h($comment['comment'])) ?></div>
          <br>
          <div class="comment-individual-picturebox">
            <?php if ($comment['image']) : ?>
              <img src="<?php echo UPLOADS_IMAGE_PATH . 'comments/' . $comment['image']?>"/>
            <?php endif ?>
          </div>
          <div class="comment-individual-timestamp">Posted on: <?php echo $comment['time_sent'] ?></div>
          <?php if (empty($comment['password'])) : ?>
            <a href="index.php?page=<?php echo $page ?>" class="button-link">Back to previous page</a>
          <?php else : ?>
            <form action="action.php" method="post">
              <input type="hidden" name="id" value="<?php echo $comment['id'] ?>" />
              <input type="hidden" name="page" value="<?php echo $page ?>" />
              <?php if (empty($errors)) : ?>
                <p>Are you sure?</p>
                <input type="hidden" name="password" value="<?php echo $comment['password'] ?>" />
                <input type="hidden" name="action" value="delete" />
                <button class="button-link" id="delete-button" type="submit" name="delete_confirm" value="1">Delete</button>
                <a class="button-link" href="index.php?page=<?php echo $page ?>">Cancel</a>
              <?php else : ?>
                <label for="password-box">Pass: </label>
                <input type="text" id="password-box" name="password" />
                <button class="button-link" id="delete-button" type="submit" name="action" value="delete">Delete</button>
              <?php endif ?>
            </form>
          <?php endif ?>
        </div> 
      </div>
    </div>
  </body>
</html>
