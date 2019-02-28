Module for SLT framework
version 0.1 beta

Before used, module need installation.
module('ImgsStorage') -> install() or url link imgs-storage/install // FOR INSTALLATION
module('ImgsStorage') -> uninstall() or url link imgs-storage/uninstall // FOR UNINSTALLATION

Code example
---------------------

Interface:

use Modules\ImgsStorage\Models\ImgsStorage;
// add new img
ImgsStorage::ins() -> set_new_img($FILES['imgnamevar']['tmp_name'], $FILES['imgnamevar']['name'], [$description=""]);

$img = ImgsStorage::ins() -> one() -> id(4);
// link to image
$img -> to_link($size); // $size = 'xl' or lg or md or sm or xs

$img -> id;
$img -> imgsB64_id; // id link to imgsB64 table
$img -> title;
$img -> description;
$img -> date_of_create;
$img -> date_of_update;

$img -> to_b64($size); // return img source in b64 

$img -> remove_this(); // true remove

// other functions is default