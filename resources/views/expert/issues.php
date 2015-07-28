<?php include __PUBLIC__ . '/assets/header.php' ?>
  <h2>YOUR JOINING TROUBLES</h2>

  <ul><?php foreach($issues as $issue){ echo
    '<li><a href="/expert/issues/'.$issue['id'].'/chat">'.$issue['title'].'</a></li>'; } ?>
  </ul>
<?php include __PUBLIC__ . '/assets/footer.php'; ?>
