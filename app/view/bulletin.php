<html>
  <head>
    <title>Challenge30 - Level 6</title>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
  </head>
  <body>
    <div id="container">
      <h2>Bulletin Board</h2>
      <div id="form-section">
        <?php if (!empty($errors)) : ?>
          <p id="notification-text">
            Notification: 
            <?php foreach ($errors as $errorMessage) : ?>
              <?php echo $errorMessage ?>
              <br>
            <?php endforeach ?>
          </p>
        <?php endif ?>
        <form action="post.php" method="post" enctype="multipart/form-data">
          <label for="title-box">Title: </label>
          <br>
          <input type="text" id="title-box" name="title" placeholder="Title must be 10-32 characters long" value="<?php echo (isset($_POST['title'])) ? h($_POST['title']) : '' ?>" />
          <br>
          <br>
          <label for="comments-box">Body: </label>
          <br>
          <textarea id="comments-box" name="comment" rows="5" placeholder="Comment must be 10-200 characters long"><?php echo (isset($_POST['comment'])) ? h($_POST['comment']) : '' ?></textarea>
          <br>
          <br>
          <label for="image-box">Insert Image</label>
          <br>
          <input type="file" name="image" id="image-box"/>
          <label for="password-box">Password: </label>
          <input type="text" id="password-box" name="password" value="<?php echo (isset($_POST['password'])) ? h($_POST['password']) : '' ?>" />
          <br>
          <br>
          <button id="submit-button" type="submit" name="submit" value="Submit" class="button-link">Submit</button>
        </form>
      </div>
      <div id="comment-section">
        <?php if (empty($comments) && isset($comments)) : ?>
          <div class="comment-individual">
            <div class="comment-individual-titlebox">Comment Empty</div>
            <div class="comment-individual-commentbox">There is no comment to show</div>
            <div class="comment-individual-timestamp"></div>
          </div> 
        <?php elseif (isset($comments)) : ?>
          <?php foreach ($comments as $comment) : ?>
            <div class="comment-individual">
              <div class="comment-individual-titlebox"><?php echo h($comment['title'])?></div>
              <div class="comment-individual-commentbox"><?php echo nl2br(h($comment['comment']))?></div>
              <div class="comment-individual-picturebox">
                <?php if ($comment['image']) : ?>
                  <img src="<?php echo UPLOADS_IMAGE_PATH . 'comments/' . $comment['image']?>"/>
                <?php endif ?>
              </div>
              <div class="comment-individual-timestamp">Posted on: <?php echo $comment['time_sent']?></div>
              <form action="action.php" method="post">
                <input type="hidden" name="id" value="<?php echo $comment['id'] ?>" />
                <label for="password-box">Pass: </label>
                <input type="text" id="password-box" name="password" />
                <input type="hidden" name="page" value="<?php echo $paginator->getPage() ?>" />
                <button id="delete-button" type="submit" name="action" value="delete" class="button-link">Del</button>
                <button id="update-button" type="submit" name="action" value="update" class="button-link">Edit</button>
              </form>
              <a class="comment-individual-deletebutton" href=></a>
            </div> 
          <?php endforeach ?>
          <div id="pagination-container">
            <?php if ($prev = $paginator->getPrev()) : ?>
              <a class="pagination" href="index.php?page=<?php echo $prev ?>">&lt;</a>
            <?php endif ?>
            <?php $pagingData = $paginator->paginate() ?>
            <?php for ($i = (int) $pagingData['start']; $i <= $pagingData['end']; $i++) : ?>
              <?php if ($i === $paginator->getPage()) : ?>
                <a class="pagination"><b>[<?php echo $i ?>]</b></a>
              <?php else : ?>
                <a class="pagination" href="index.php?page=<?php echo $i ?>"><?php echo $i ?></a>
              <?php endif ?>
            <?php endfor ?>
            <?php if ($next = $paginator->getNext()) : ?>
              <a class="pagination" href="index.php?page=<?php echo $next ?>">&gt;</a>
            <?php endif ?>
          </div>
        <?php endif ?>
      </div>
    </div>
  </body>
</html>
