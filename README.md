## PHAR Builer
PHAR Builder is a command-line script for PHP, which allows you to create a PHP Archive (PHAR) file from your PHP project.

PHAR files are a way to package PHP applications into a single file, making it easy to distribute and deploy your application.

### Usage
To create a PHAR file for a single PHP file, you can run in CLI:
```
php buildPhar.php -s /path/to/your_file.php -o /path/to/output_file.phar
```

To create a PHAR file for an entire PHP application (directory), run in CLI:
```
php buildPhar.php -s /path/to/source_directory -o /path/to/output_file.phar -a myApp
```

### Supported Options
- [`-s, --source`] - The path to the PHP file or directory to be included in the PHAR file. This option is mandatory.
- [`-o, --output`] - The path to the output PHAR file. This option is mandatory.
- `-c, --compression` - The compression algorithm to use for the PHAR file: "GZ" (gzip) or "BZ2" (bzip2)
- `-m, --main` - The main file to be loaded when your PHAR archive is parsed (default is "index.php")
- `-a, --alias` - Alias for the PHAR archive, so you can use your project files directly from the archive
- `-h, --help` - Display help information for the script

### Requirements
- PHP 8.0 or later (might work with earlier versions, but it hasn't been tested)
- PHP extensions:
  - PHAR installed and enabled
  - mbstring installed and enabled
- To create PHAR archives you need `phar.readonly=off` (either in your php.ini or when invoking php: `php -d phar.readonly=off buildPhar.php ...`
- Operating systems: Windows, Linux, macOS
- To use compression you will also need the PHP extension(s):
  - zlib extension for `GZ` compression
  - bz2 extension for `BZ2` compression

### License
This software is licensed under the MIT License. This means that you are free to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the software. However, you must include the original copyright and license notice in any copies.
- In no event shall the authors or copyright holders be liable for any claim, damages or other liability, whether in an action of contract, tort or otherwise, arising from, out of or in connection with the software or the use or other dealings in the software.
