<?php require __DIR__.'/../includes/bootstrap.php';site_header('Home','home');?>
<section class="hero"><h1>Find Your Loved Ones</h1><h1>A Gift of Love and Remembrance</h1><p>Search and locate graves with our interactive cemetery mapping system</p>
<form class="sbox" action="search.php"><input name="q" placeholder="Search for graves by name, date, plot number, or section" aria-label="Search graves"><button class="btn"><i class="fas fa-search"></i> Search</button></form>
<a class="btn line" href="map.php">View Cemetery Map</a></section>
<section class="wrap"><h2 style="text-align:center;font-size:38px;margin:50px 0 30px">Our Features</h2><div class="feat">
<div><i class="fas fa-search"></i><h3>Search Records</h3><p>Find a grave by name, date, plot or section.</p></div>
<div><i class="fas fa-map-marked-alt"></i><h3>Interactive Map</h3><p>See exactly where a plot is and whether it is available.</p></div>
<div><i class="fas fa-calendar-check"></i><h3>Online Reservation</h3><p>Request a plot online and track the status of your request.</p></div></div></section>
<?php site_footer();
