<?php require_once('config.php'); ?>

<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js ie6" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->  <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	
	<title><?php echo(PRESENTATION_TITLE); ?></title>
</head>
<body>
  <h1><?php echo(PRESENTATION_TITLE); ?></h1>
  <p>
    If you have any feedback at all please <a href="mailto:phil@pusher.com">drop me an email</a> or <a href="http://twitter.com/leggetter">send me a tweet</a>.
  </p>

  <ul>
    <li>
      <a href="pres">Slides</a>
    </li>
    <li>
      <a href="interact">2nd Screen App</a>
    </li>
    <li>
      <a href="pres/?<?php echo(CONTROLLER_TOKEN); ?>">Slides (As Controller of 2nd Screen app)</a>
    </li>
  </ul>
  
</body>
</html>