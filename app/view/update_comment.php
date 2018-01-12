<html>
  <head>
    <title>Challenge30 - Level 6</title>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
  </head>
  <body>
    <div id="container">
    <h2>Update this comment?</h2>
      <div id="form-section">
        <?php if (!empty($errors)) : ?>
          <p id="notification-text">
            <?php foreach ($errors as $errorMessage) : ?>
              <?php echo $errorMessage ?>
              <br>
            <?php endforeach ?>
          </p>
        <?php endif ?>
        <?php if ($passwordMatch) : ?>
          <form action="action.php" method="post" enctype="multipart/form-data">
            <label for="title-box">Title: </label>
            <br>
            <input type="text" id="title-box" name="title" placeholder="Title must be 10-32 characters long" value="<?php echo h($comment['title']) ?>" />
            <br>
            <br>
            <label for="comments-box">Body: </label>
            <br>
            <textarea id="comments-box" name="comment" rows="5" placeholder="Comment must be 10-200 characters long"><?php echo h($comment['comment']) ?></textarea>
            <br>
            <br>
            <div class="comment-individual-picturebox">
              <?php if ($comment['image']) : ?>
                <img src="<?php echo UPLOADS_IMAGE_PATH . 'comments/' . $comment['image']?>"/>
                <input type="checkbox" id="delete_image_checkbox" name="delete_image"/>
                <label for="delete_image_checkbox">Delete image?</label>
              <?php endif ?>
            </div>
            <label for="image-box">Insert image:</label>
            <input type="file" name="image" id="image-box"/>
            <input type="hidden" name="id" value="<?php echo $comment['id'] ?>" />
            <input type="hidden" name="password" value="<?php echo $comment['password'] ?>" />
            <input type="hidden" name="page" value="<?php echo $page ?>" />
            <input type="hidden" name="action" value="update" />
            <br>
            <br>
            <button class="button-link" id="submit-button" type="submit" name="update_confirm" value="1">Submit</button>
            <a class="button-link" href="index.php?page=<?php echo $page ?>">Cancel</a>
          </form>
        <?php else : ?>
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
                  <label for="password-box">Pass: </label>
                  <input type="text" id="password-box" name="password" />
                  <button class="button-link" id="delete-button" type="submit" name="action" value="update">Edit</button>
                </form>
              <?php endif ?>
            </div> 
          </div>
        <?php endif ?>
      </div>
    </div>
  </body>
</html>
