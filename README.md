# Notes

A mobile-first [Google Keep](https://keep.google.com/)-like that use the [Kanboard](https://kanboard.net/) API.

## Prerequisites

  - PHP 5.5 or newer
  - A web server with URL rewriting

## Installation

  - Clone this repo, and then the usual `composer install`
  - Create a virtual host that point to the `public/` directory (I'll let you Google for that)

## Configuration

Configuration parameters are in `src/settings.php`. You'll need to change parameters under the
`kanboard` key only (unless you know what you're doing).

Explanations about available parameters can be found in this file as well.

About the web server configuration: if you encounter troubles please refer to [this page](http://www.slimframework.com/docs/start/web-servers.html).

## Usage

Go to the URL of your virtual host (e.g `tasks.mydomain.com`). You'll see the tasks (notes) from you Kanboard
instance according to your configuration. You can then perform several operations like create new notes,
delete notes, etc.

**Notes** is intended to be mobile-first.

## How it works

**Notes** is built on [Vue.js](http://vuejs.org/) for the frontend, [Slim](http://www.slimframework.com/) (PHP)
for the backend, and JSON-RPC to interact with the [Kanboard's API](https://kanboard.net/documentation/api-json-rpc).