<?php
  $env = getenv('HONEYBASE_ENV');
  if($env === false){
    $env = "d";
  }
 ?>

<?php if ($env == "p") { echo '
{
  "token":"437440486425715",
  "pubsub_endpoint":"http://pubsub.techfund.jp:8001/pubsub"
}
'; } elseif ($env == "s") { echo '
{
  "token":"438227309680366",
  "pubsub_endpoint":"http://ec2-52-68-142-14.ap-northeast-1.compute.amazonaws.com:8001/pubsub",
  "_pubsub_endpoint":"http://pubsub.techfund.jp:8001/pubsub"
}
'; } else { echo '
{
  "token":"437440596425704",
  "pubsub_endpoint":"http://pubsub.techfund.jp:8001/pubsub"
}
'; } ?>
