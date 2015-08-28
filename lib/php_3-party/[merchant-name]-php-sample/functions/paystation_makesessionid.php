<?
function makePaystationSessionID($min=8,$max=8){

  # seed the random number generator - straight from PHP manual
  $seed = (double)microtime()*getrandmax();
  srand($seed);

  # make a string of $max characters with ASCII values of 40-122
  $p=0; while ($p < $max):
    $r=123-(rand()%75);
    $pass.=chr($r);
  $p++; endwhile;

  # get rid of all non-alphanumeric characters
  $pass=ereg_replace("[^a-zA-NP-Z1-9]+","",$pass);

  # if string is too short, remake it
  if (strlen($pass)<$min):
    $pass=makePaystationSessionID($min,$max);
  endif;

  return $pass;

};
?>