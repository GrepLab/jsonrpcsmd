## JSON-RPC Service Mapping Description builder

This library make a reflection of the classes given and output a json using the v2 of the [proposal specification published in simple-is-better.org](http://www.simple-is-better.org/json-rpc/jsonrpc20-smd.html).

### Usage

To start use this library just create an instance of the Smb class. This class is the core of the library. You only need work this Smb.
The Smb class only need to know the url of the endpoint witch is where the calls will be sended. You can pass the url in the constructor or set later using the setTarget() method. But remember, you cannot generate the json map if the target is not setted. 

    //You can start laki this:
    $smd = new \Greplab\Jsonrpcsmd\Smd('http://my-website/path/of/the/endpoint');
    
    //Or like that:
    $smd = new \Greplab\Jsonrpcsmd\Smd();
    $smd->setTarget('http://my-website/path/of/the/endpoint');
    
#### Canonical URL

If the "canonical" option is enabled each method will have a diferent endpoint consisting in the default endpoint url plus the name of the service and method. This is useful when you use a tool like firebug to monitor the ajax calls. This way each call is easily recognizable but require the server recognize and identify this last seccion as the service and method path. This option is disabled by default.

To change this use:
    $smd->setUseCanonical(true);

#### Add classes

To index a class simple execute:

    $smd->addClass('ClassNameToIndex');

You have to call this method for each class you want to index.

#### Build the json map

To get the json map just print the Smd instance to the browser. If you want the json before to send the browser use the toJson() method. 

    $json = $smd->toJson();
    print($json);

### License

This library is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
