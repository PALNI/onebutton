# OneButton
OneButton is a link resolving application designed to streamline resource sharing for consortia.  OneButton can be used to replace multiple resource sharing options with a single request button, which checks available group circulation holdings and availability and routes the user to the appropriate request form.

OneButton was designed for the WorldCat Local/Discovery environment and relies on the OCLC Availability API, but could be adapted using any library service platform with a holdings/availability API that displays group holdings.

## Requirements

- PHP 5.6+
- Composer
- A WSKey for the OCLC WMS Availability API
- The OCLC PHP Auth Library

## Installation

At the application (domain) root, install dependencies with Composer.  Sample composer.json file:

```javascript
{
  "name" : "MyApp",
  "repositories":
  [
    {
      "type": "git",
      "url": "https://github.com/OCLC-Developer-Network/oclc-auth-php.git"
    }
  ],
  "require" :
  {
    "OCLC/Auth" : ">=3.0"
  }
}
```

Create a subdirectory for each institution and clone this repository in that subdirectory.

The application's base URL will look like this:

http://somewebsite.edu/institution/link.php?

Within the institution's subdirectory, copy config.php.sample to config.php and fill in all values.

Most values can be obtained from the OCLC Developer's Network and are associated with your WSKey credentials.

## Testing

You can test the application prior to implementing by appending a well-formed OpenURL request to 
the base URL of your application, for example:

http://somewebsite.edu/institution/link.php?url_ver=Z39.88-2004&rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Abook&rft.genre=book&rft.genre=book&rft_id=info%3Aoclcnum%2F54987177&rft_id=urn%3AISBN%3A9780195285062&rft.btitle=The+book+of+common+prayer+%3A+and+administration+of+the+sacraments+and+other+rites+and+ceremonies+of+the+church&rft.date=1993&rft.isbn=9780195285062&rft.aucorp=Episcopal+Church.&rft.place=New+York&rft.pub=Oxford+University+Press&req_dat=&rfe_dat=54987177

## Implementation

You can set up OneButton for WorldCat Local or Discovery in OCLC Service Configuration.

- Set up the base URL of your OneButton installation as an OpenURL 1.0 link resolver
- Reference the OneButton  OpenURL Resolver you set up under Resource Sharing (Any Level) and give it a label (suggested label: Get It)
- OneButton is designed to only work with print book materials.  Under the fulfillment button display settings, set the OneButton resolver to only display for monographs owned by your resource sharing network.  Use general ILL fulfillment buttons for other types (serials, articles, etc.).  You can also set it up to display for monographs regardless of ownership (i.e., if you own it, the resource sharing network owns it, or it is not owned at all - OneButton will direct users to the ILL form you indicate in config.php.




