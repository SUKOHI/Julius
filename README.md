Julius
====

A PHP package mainly developed for Laravel to manage calendar and events.  
(This package was inspired by [laravel-calendar
](https://github.com/makzumi/laravel-calendar)).  
Thank you makzumi!  
(This is for Laravel 5+. [For Laravel 4.2](https://github.com/SUKOHI/Julius/tree/1.0))

![Imgur](http://i.imgur.com/J8o5f2N.png)

Installation
====

Add this package name in composer.json

    "require": {
      "sukohi/julius": "2.*"
    }

Execute composer command.

    composer update

Register the service provider in app.php

    'providers' => [
        ...Others...,  
        Sukohi\Julius\JuliusServiceProvider::class,
    ]

Also alias

    'aliases' => [
        ...Others...,  
        'Julius'   => Sukohi\Julius\Facades\Julius::class
    ]

Usage
====

**Minimal Way**
    
	echo \Julius::make();
    
    
**with Options**

* See [Methods](#methods) for the details.  
  

    $events = [
        date('Y-m') .'-11 10:15:00' => ['Event 1', 'Event 2'],
        date('Y-m') .'-09 10:26:00' => ['Event 3', '<strong>Event 4</strong>'],
        date('Y-m') .'-07 14:12:23' => ['Event 5'],
        date('Y-m') .'-15 12:39:00' => ['Event 6'],
        date('Y-m') .'-17 25:50:00' => ['Event 7'], // You can use times over 24:00
    ];
    
	$julius = \Julius::make();
	
	$julius->setStartDate(\Request::get('base_date'))	//Set base date
		->showNavigation(true)	// Show or hide the navigation
		->showDayOfWeek(true)	// Show or hide the day of week for "week" or "day" mode.
		->setMode(\Request::get('mode'))	// month, week or day
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
		->setDateFormats([  // Set date formats
             'year_month' => 'm Y',
             'day' => 'w j',
             'time' => 'H:i'
         ]);
    echo $julius->generate();

Methods<a name="methods"></a>
====

**showNavigation($bool)**

* Show or hide the navigation

**showDayOfWeek($bool)**

* Show or hide the day of week for `week` or `day` mode.

**setMode($str)**

* To set calendar type. $str can be month, week or day.

**setHours($start, $end)**

* To set the hour range for `day` and `week` mode.
* You need to set time formatlike `00:00`, `10:25` for $start and $end.
* You can set times over 24:00

**setClasses($array)**

* To set class values like the below.

  
	setClasses([
		'table' => 'table table-bordered', 
		'header' => 'table-header', 
		'time' => 'time', 
		'prev' => 'btn', 
		'next' => 'btn', 
		'day_label' => 'text-success', // You also can set array like ['0' => 'sunday-class', '6' => 'saturday-class']
		'today' => 'text-danger',
		'year_month' => 'text-center', 
		'day' => 'text-muted', // You also can set array like ['0' => 'sunday-class', '6' => 'saturday-class']
	])

**setWraps($array)**
	
* To set wrapers.


	setWraps([
		'event' => ['<p>', '</p>'], 
		'day' => ['<div>', '</div>'], 
		'date' => ['<span>', '</span>']
	])

**setIcons($array)**

* To set navigation icons. (You can use HTML tags)


	setIcons([
		'prev' => '<span class="glyphicon glyphicon-arrow-left"></span> Prev', 
		'next' => 'Next <span class="glyphicon glyphicon-arrow-right"></span>'
	])

**setNavigationJsFunction($js_function_name)**

* When using this method, the navigation icon link has onclick event like the below.


	onclick="js_function_name('2015-09')"

**setDayLabels($array)**

* To set week day names


	setDayLabels(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'])

**setMonthLabels($array)**

* To set month names.


	setMonthLabels([
		'January', 
		'February', 
		'March', 
		'April', 
		'May', 
		'June', 
		'July', 
		'August', 
		'September', 
		'October', 
		'November', 
		'December'
	])

**setInterval($str)**

* To set time step. $str can be +3 hours, +30 minutes etc...

**setEvents($events, $closure)**

* To set events and its callback(closure)


	setEvents($events, function($event, $start_dt, $end_dt){  
	
		// Here you can make content for a specific event.
		return $start_dt->day .' - '. $start_dt .' - '. $end_dt;
		
	})
	
	// $start_dt and $end_dt are Carbon object.

**setDateFormats($array)**
   
* To set date formats.
* See [here](http://php.net/manual/en/function.date.php) for other date symbols.


	setDateFormats([
		'year_month' => 'm Y',
		'day' => 'w j',
		'time' => 'H:i'
	])

License
====
This package is licensed under the MIT License.

Copyright 2015 Sukohi Kuhoh