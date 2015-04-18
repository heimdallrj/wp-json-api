<?php
/**
 * author: @_thinkholic
 * 14042015
 */
 
# TimeZone Settings
date_default_timezone_set("UTC");

$timestamp = date("Y-m-d H:i:s", time());

# Custom-functions

# status
$status = array(  
    100 => 'Continue',
    101 => 'Switching Protocols',
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoritative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    300 => 'Multiple Choices',
    301 => 'Moved Permanently',
    302 => 'Found',
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    307 => 'Temporary Redirect',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    402 => 'Payment Required',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Time-out',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Precondition Failed',
    413 => 'Request Entity Too Large',
    414 => 'Request-URI Too Large',
    415 => 'Unsupported Media Type',
    416 => 'Requested range not satisfiable',
    417 => 'Expectation Failed',
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable',
    504 => 'Gateway Time-out',
    505 => 'HTTP Version not supported'
);

# respond inits
$data['statusCode'] = 400;

# headers
header("Access-Control-Allow-Orgin: *");
header("Access-Control-Allow-Methods: *");

# respond inits
$data['validToken'] = FALSE;
$data['success'] = FALSE;
$data['data'] = FALSE;
$data['requestTimestamp'] = $timestamp;

$data['statusCode'] = 400;
	
# [JSON]
if ( (isset( $_REQUEST['r'] )) && ( $_REQUEST['r'] == 'json' ) )
{   
    $data['statusCode'] = 200;

	$data['contentType'] = 'application/json';
	
	$data['site']['name'] = get_bloginfo('name');
	$data['site']['description'] = get_bloginfo('description');
	
	# ID
	if ( (isset($_REQUEST['ID'])) )
	{
		$ID = $_REQUEST['ID'];
		
		if ( (get_post_type( $ID )) && (get_post_status( $ID )=='publish') )
		{
			$data['post']['ID'] = $ID;
			$data['post']['post_type'] = get_post_type( $ID );
			
			$args = array(
				"page_id" => "$ID",
			);
			
			$the_query = new WP_Query( $args );
			
			if ( $the_query->have_posts() )
			{
				while ( $the_query->have_posts() )
				{
					$the_query->the_post();
					
					$data['post']['title'] = get_the_title();
					$data['post']['content'] = get_the_content();
					$data['post']['excerpt'] = get_the_excerpt();
					$data['post']['author'] = get_the_author();
					$data['post']['date'] = get_the_date();
					$data['post']['thumbnail'] = wp_get_attachment_url (get_post_thumbnail_id( get_the_ID() ));
					$data['post']['meta'] = get_post_meta( get_the_ID() );
					
					unset($data['post']['meta']['_wp_page_template']);
					unset($data['post']['meta']['_edit_lock']);
					unset($data['post']['meta']['_edit_last']);
					unset($data['post']['meta']['_thumbnail_id']);
				}
			}
			else
			{
				$data['statusCode'] = 404;
			}
			
			wp_reset_postdata();
			
			$data['success'] = TRUE;
		}
		else
		{
			$data['statusCode'] = 400;
		}
	}
	
	# TYPE
	if ( (isset($_REQUEST['TYPE'])) )
	{
		$TYPE = $_REQUEST['TYPE'];
		
		$args = array(
			"post_type" => "$TYPE",
		);
		
		$the_query = new WP_Query( $args );
			
		if ( $the_query->have_posts() )
		{
			$i=0;
			
			while ( $the_query->have_posts() )
			{
				$the_query->the_post();
				
				$data['posts'][$i]['title'] = get_the_title();
				$data['posts'][$i]['content'] = get_the_content();
				$data['posts'][$i]['excerpt'] = get_the_excerpt();
				$data['posts'][$i]['author'] = get_the_author();
				$data['posts'][$i]['date'] = get_the_date();
				$data['posts'][$i]['thumbnail'] = wp_get_attachment_url (get_post_thumbnail_id( get_the_ID() ));
				$data['posts'][$i]['meta'] = get_post_meta( get_the_ID() );
				
				unset($data['posts'][$i]['meta']['_wp_page_template']);
				unset($data['posts'][$i]['meta']['_edit_lock']);
				unset($data['posts'][$i]['meta']['_edit_last']);
				unset($data['posts'][$i]['meta']['_thumbnail_id']);
				
				$i++;
			}
		}
		else
		{
			$data['statusCode'] = 404;
		}
		
		wp_reset_postdata();
		
		$data['success'] = TRUE;
	}
}

# header status
$data['status'] = $status[$data['statusCode']];

# headers again
header('Content-Type: application/json');
header("HTTP/1.1 " . $data['statusCode'] . " " . $data['status']);

# return respond
print json_encode($data);

// EOF.