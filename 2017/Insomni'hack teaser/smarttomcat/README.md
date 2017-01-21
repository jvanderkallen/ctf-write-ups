# smarttomcat

## Details
* Category: web
* Points: 50
* Authors: xel and grimmlin

## Description
> Normal, regular cats are so 2000 and late, I decided to buy this allegedly smart tomcat robot
> Now the damn thing has attacked me and flew away. I can't even seem to track it down on the broken search interface... Can you help me ?
>
> [Search interface](http://smarttomcat.teaser.insomnihack.ch/)

## Write-up
The name and the description of the challenge indicate that we're probably dealing with a web application that is run with Tomcat.

The page shows you a map and allows you to enter latitude and longitude coordinates. The page says that we have to find the coordinates of the "LOST CAT" and that a reward will be given if it's found.

If you submit the coordinates a POST request is made to `http://smarttomcat.teaser.insomnihack.ch/index.php` with the following data `u=http://localhost:8080/index.jsp?x=3&y=3` where x and y are the latitude and the longitude coordinates respectively. It looks like the PHP script sends a request to the provided URL and returns the result. This tells us that the page contains a Server Side Request Forgery (SSRF) vulnerability. I tried looking for files that are accessible through http://localhost:8080/, but I couldn't find anything interesting.

Eventually I remember that Tomcat has a [Manager App](https://tomcat.apache.org/tomcat-6.0-doc/manager-howto.html). It is usually accessible through `http://{host}:{port}/manager/html`. A quick POST request showed that the Manager App was indeed running, but unfortunately we needed to provide credentials to access it. After some guessing I managed to find the correct username and password. Executing `curl 'http://smarttomcat.teaser.insomnihack.ch/index.php' --data-urlencode 'u=http://tomcat:tomcat@localhost:8080/manager/html'` gives us the flag: "We won't give you the manager, but you can have the flag : INS{th1s_is_re4l_w0rld_pent3st}".