Julius
====

A PHP package mainly developed for Laravel to manage calendar and events.  
(This package was inspired by https://github.com/makzumi/laravel-calendar).  
Thank you makzumi!

Installation&settings for Laravel
====

After installation using composer, add the followings to the array in  app/config/app.php

    'providers' => array(  
        ...Others...,  
        'Sukohi\Julius\JuliusServiceProvider',
    )

Also

    'aliases' => array(  
        ...Others...,  
        'Julius' => 'Sukohi\Julius\Facades\Julius',
    )

Usage
====  

	$julius = Julius::make();
	
	/*  Optional Methods  */

	$julius->setStartDate(Input::get('base_date'))	//Set base date
		->showNavigation(true)	// Show or hide the navigation
		->setMode(Input::get('mode'))	// month, week or day
		->setHours(8, 20)	// Set the hour range for day and week mode
		->setClasses([	// Set classes
				'table' => 'table table-bordered', 
				'header' => 'table-header', 
				'time' => 'time', 
				'prev' => 'btn', 
				'next' => 'btn', 
				'day_label' => 'text-success', 
				'today' => 'text-danger'
		])
		->setWraps([	// Set wrapers
				'event' => ['<p>', '</p>'], 
				'day' => ['<div>', '</div>'], 
				'date' => ['<span>', '</span>']
		])
		->setIcons([	// Set navigation icons (You can use HTML tags)
				'prev' => '<span class="glyphicon glyphicon-arrow-left"></span> Prev', 
				'next' => 'Next <span class="glyphicon glyphicon-arrow-right"></span>'
		])
		->setDayLabels(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'])	// Set week day names
		->setMonthLabels(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'])	// Set month names
		->setInterval('+10 minutes')	// Set interval minutes for week and day mode
		->setEvents($events, $callback = function($events, $start_dt, $end_dt){	// Set events and its callback function(Callback is optional)
			
			$html =  '<pre>'. print_r($events, true) .'</pre>';
			$html .= '<pre>'. print_r($start_dt, true) .'</pre>';
			$html .= '<pre>'. print_r($end_dt, true) .'</pre>';
			return $html;
			
		})
		->setDateFormats(['year_month' => 'm Y', 'time' => 'H:i']);	// Set date formats
    echo $julius->generate();
    
License
====
This package is licensed under the MIT License.

Copyright 2015 Sukohi Kuhoh