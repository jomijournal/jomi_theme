<?php
/**
 * Template Name: Pricing
 */
?>

<div class="pricing">
	<div class="row">
		<div class="col-xs-12">
			<h1>Subscribe to the World's Highest Quality Surgical Video Journal</h1>
		</div>
	</div>
	<div class="plans row">
		<div class="student col-xs-12 col-sm-4 col-md-3">
			<div class="plan-header">
				<h2>Students</h2>
			</div>
			<div class="plan-body">
				<p class="desc">For inquisitive pre-medical and medical students</p>
				<p class="as-low-as">As low as</p>
				<p class="price">$99/year</p>
			</div>
			<div class="plan-form">
				<p>
					<input id="student-monthly" type="radio" name="student-option" value="1000">
					Monthly &nbsp;&nbsp;
					($10.00/mo.)
				</p>
				<p>
					<input id="student-annually" type="radio" name="student-option" value="825" checked>
					Annually &nbsp;&nbsp;
					($8.25/mo.)
				</p>
			</div>
			<div class="plan-cost">
				<p class="price">$99.00</p>
			</div>
		</div>
		<div class="resident col-xs-12 col-sm-4 col-md-3">
			<div class="plan-header">
				<h2>Residents</h2>
			</div>
			<div class="plan-body">
				<p class="desc">For apprehensive medical and surgical residents</p>
				<p class="as-low-as">As low as</p>
				<p class="price">$999/year</p>
			</div>
			<div class="plan-form">
				<p>
					<input id="resident-monthly" type="radio" name="resident-option" value="10000">
					Monthly &nbsp;&nbsp;
					($100.00/mo.)
				</p>
				<p>
					<input id="resident-annually" type="radio" name="resident-option" value="8325" checked>
					Annually &nbsp;&nbsp;
					($83.25/mo.)
				</p>
			</div>
			<div class="plan-cost">
				<p class="price">$999.00</p>
			</div>
		</div>
		<div class="attending col-xs-12 col-sm-4 col-md-3">
			<div class="plan-header">
				<h2>Attendings</h2>
			</div>
			<div class="plan-body">
				<p class="desc">For adaptive surgeons and attending physicians</p>
				<p class="as-low-as">As low as</p>
				<p class="price">$1998/year</p>
			</div>
			<div class="plan-form">
				<p>
					<input id="attending-monthly" type="radio" name="attending-option" value="20000">
					Monthly &nbsp;&nbsp;
					($200.00/mo.)
				</p>
				<p>
					<input id="attending-annually" type="radio" name="attending-option" value="16650" checked>
					Annually &nbsp;&nbsp;
					($166.50/mo.)
				</p>
			</div>
			<div class="plan-cost">
				<p class="price">$1998.00</p>
			</div>
		</div>
		<div class="institution col-xs-12 col-sm-12 col-md-3">
			<div class="plan-header">
				<h2>Institutions</h2>
			</div>
			<div class="plan-body">
				<p class="desc">For progressive medical and teaching institutions</p>
				<p class="as-low-as">As low as</p>
				<p class="price">$100/video/year</p>
			</div>
			<div class="plan-form">
				<p>Contact lib@jomi.com pls</p>
			</div>
		</div>
	</div>
</div>

<script>
$(function() {
	$('input[type="radio"]').click(function(e) {
		var value = $(this).attr('value');

		var price_display = $(this).parent().parent().parent().find('.plan-cost .price');

		var price = (value/100) * 12;
		price.toFixed(3);
		price.toFixed(2);
		// taken from http://stackoverflow.com/questions/149055/how-can-i-format-numbers-as-money-in-javascript
		price = price.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');

		var price_text = '$' + price;

		price_display.html(price_text);
	});
});

</script>