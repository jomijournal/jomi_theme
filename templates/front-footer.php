<footer>
  <div class='row'>
	  <h4>Preview content on:</h4>
  </div>
  <div class='row'>
	  <div class='col-sm-8 col-sm-offset-2'>
		  <nav class="nav-bottom">
		    <ul>
		      <div class='col-md-4'>
		      	<li><a href="/orthopedics/" class="border">Orthopedics</a></li>
		      </div>
		      <div class='col-md-4'>
		      	<li><a href="/general/" class="border">General Surgery</a></li>
		      </div>
		      <div class='col-md-4'>
		      	<li class='coming-soon'><a href="/ophthalmology/" class="border">Ophthalmology</a></li>
		      </div>
		    </ul>
		  </nav>
	  </div>
  </div>
</footer>

<script>
$('li').each(function(index){
	if($(this).hasClass('coming-soon')) {
		og = $(this).find('a').text();
		$(this).hover( function() {
			$(this).find('a').text('Coming Soon');
		}, function() {
			$(this).find('a').text(og);
		});
	}
});
</script>