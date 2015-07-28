<?php include __PUBLIC__ . '/assets/header.php' ?>
  <h2 style="margin-top: 200px;">YOUR JOINING TROUBLES</h2>

  <ul><?php if ( count($issues) > 0 ) { foreach($issues as $issue){ echo
    '<li><a href="/expert/issues/'.$issue['id'].'/chat">'.$issue['title'].'</a></li>'; }
    } else { echo
    '現在ご依頼されている案件はございません。';
    } ?>
  </ul>
<?php include __PUBLIC__ . '/assets/footer.php'; ?>
