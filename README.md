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
    
    $events = [
        date('Y-m') .'-11 10:15:00' => ['Event 1', 'Event 2'],
        date('Y-m') .'-09 10:26:00' => ['Event 3', '<strong>Event 4</strong>'],
        date('Y-m') .'-07 14:12:23' => ['Event 5'],
        date('Y-m') .'-15 12:39:00' => ['Event 6'],
        date('Y-m') .'-17 25:50:00' => ['Event 7'], // You can use times over 24:00
    ];
    
	$julius = Julius::make();
	
	/*  Optional Methods  */

	$julius->setStartDate(Input::get('base_date'))	//Set base date
		->showNavigation(true)	// Show or hide the navigation
		->showDayOfWeek(true)	// Show or hide the day of week for "week" or "day" mode.
		->setMode(Input::get('mode'))	// month, week or day
		->setHours('8:10', '18:20')	// Set the hour range for day and week mode. And you can use times over 24:00
		->setClasses([	// Set classes
				'table' => 'table table-bordered', 
				'header' => 'table-header', 
				'time' => 'time', 
				'prev' => 'btn', 
				'next' => 'btn', 
				'day_label' => 'text-success', // You can use array like ['0' => 'sunday-class', '6' => 'saturday-class']
				'today' => 'text-danger',
				'year_month' => 'text-center', 
				'day' => 'text-muted', // You can use array like ['0' => 'sunday-class', '6' => 'saturday-class']
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
		->setNavigationJsFunction('your_function_name')    // When using this method, the navigation icon link has onclick event. e.g) onclick="your_function_name(date)".
		->setDayLabels(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'])	// Set week day names
		->setMonthLabels(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'])	// Set month names
		->setInterval('+10 minutes')	// e.g.) +3 hours, +30 minutes etc...
		->setEvents($events, $callback = function($events, $start_dt, $end_dt){	// Set events and its callback function(Callback is optional)
			
			$html = '<div>'. $start_dt->day .'</div>';
			$html .=  '<pre>'. print_r($events, true) .'</pre>';
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