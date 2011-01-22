<?php
/**
 * Licensed under the MIT License
 * 
 *  Copyright (c) 2011 Sebastian Müller <info@sebastian-mueller.net>
 * 
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 * 
 *  The above copyright notice and this permission notice shall be included in
 *  all copies or substantial portions of the Software.
 * 
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

 require('lib/GooglShortener.php');
 
 // get your own goo.gl API key here: <https://code.google.com/apis/console/>
 $googl = new GooglShortener('AIzaSyBLDeXeCl9BHBIbozzBmhCN0hbHnvge2zE');
 
 // shorten a single URL
 echo '<h2>Shorten a single URL</h2>';
 $url = $googl->shorten('http://www.sebastian-mueller.net/');

 echo 'Short URL: '.$url->id.'<br />';
 echo 'Long URL: '.$url->longUrl.'<br /><br /><br />';
 
 
 // shorten a array of URLs
 echo '<h2>Shorten an array of URLs</h2>';
 $urls = $googl->shorten(array('http://github.com/SebastianM', 'http://news.ycombinator.com/'));
 
 foreach($urls as $u) {
   echo 'Short URL: '.$u->id.'<br />';
   echo 'Long URL: '.$u->longUrl.'<br /><br /><br />';
 }
 
 
 /**
  * expand a single shortened URL and look up analytics
  * you can also use an array of mutiple links
  */
 echo '<h2>Expand a single shortened URL, you can also use an array of URLs</h2>';
 $longUrl = $googl->expand('http://goo.gl/bWJJ');
 
 echo 'Short URL: '.$longUrl->id.'<br />';
 echo 'Long URL: '.$longUrl->longUrl.'<br />';
 echo 'Status: '.$longUrl->status.'<br />';
 echo 'Created (ISO 8601): '.$longUrl->created.'<br /><br />';
 
 /**
  * all time analytics
  * you can also use month, week, day and twoHours instead of allTime
  */
 echo 'Short URL clicks all time: '.$longUrl->analytics->allTime->shortUrlClicks.'<br />';
 echo 'Long URL clicks all time: '.$longUrl->analytics->allTime->LongUrlClicks.'<br /><br />';
 
 // referrers
 echo '----- Referrers all time: <br />';
 foreach($longUrl->analytics->allTime->referrers as $ref) {
   echo 'Ref: '.$ref->id.'<br />';
   echo 'Count: '.$ref->count.'<br /><br />';
 }
 echo '<br />';
 
 // countries
 echo '----- Countries all time: <br />';
 foreach($longUrl->analytics->allTime->countries as $c) {
   echo 'Country: '.$c->id.'<br />';
   echo 'Count: '.$c->count.'<br /><br />';
 }
 echo '<br />';

  // browsers
 echo '----- Browsers all time: <br />';
 foreach($longUrl->analytics->allTime->browsers as $b) {
   echo 'Browser: '.$b->id.'<br />';
   echo 'Count: '.$b->count.'<br /><br />';
 }
 echo '<br />';
   
   // platforms
  echo '----- platforms all time: <br />';
  foreach($longUrl->analytics->allTime->platforms as $p) {
    echo 'Platform: '.$p->id.'<br />';
    echo 'Count: '.$p->count.'<br /><br />';
  }
?>