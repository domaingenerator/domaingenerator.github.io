<?php
	/*
	*
	* ---------------------------
	* | Domain Name Generator   |
	* ---------------------------
	*
	* @Author: A.I Raju
	* @License: MIT
	* @Copyright: 2023
	*
	*/
	
	/* Default Response Header */
	header( 'Content-Type: application/json' );


	/*
	* Useful Functions
	*/
	function filter_all( $data )
	{
		/* Remove HTML Tags */
		$data = strip_tags( $data );
		
		/* replace any suspicious element from string to prevent any ATTACK */
		$data = str_replace(array('<?php', '<?', '?>', '<?=', '<script>', '</script>', '?>', '(//', '<a', '<iframe', '</a>', '</iframe>', '/>', 'alert(', 'promot(', 'onload="', 'onerror="', 'OR 1', 'or 1', 'where 1=1', 'WHERE 1=1', 'where 1 = 1', 'WHERE 1 = 1'), ' ', $data);

		/* then return clean @data */
		return $data;
	}
	
	
	/* Important Variable */
	$has_error = false;
	
	$output = []; /* Output Array */
	
	$lang = 'eng'; /* Domain name language */
	
	$domainLength = 15; /* Default domain name length */
	
	
	/* Domain Generation Limit */
	$generatorLimit = 100;
	
	
	/*
	* Data processing has been started...
	*/
	if( isset( $_POST['domain_keyword'] ) && isset( $_POST['tld'] ) )
	{
		$sn = isset( $_POST['sn'] ) && $_POST['sn'] === 'no' ? 'true':'false';
		$inb = isset( $_POST['inb'] ) && $_POST['inb'] === 'yes' ? 'true':'false';
		$domainLength = isset( $_POST['dl'] ) && $_POST['dl'] > 0 ? filter_all( $_POST['dl'] ):$domainLength;
		$domain_keyword = filter_all( $_POST['domain_keyword'] );
		$tlds = isset( $_POST['tld'] ) && !empty( $_POST['tld'] ) ? $_POST['tld']:['com', 'net', 'org'];
		
		$domainExt = '';
		foreach( $tlds as $tld )
		{
			$domainExt .= $tld.',';
		}
		
		$domainExt = substr( $domainExt, 0, -1 );
		

		
		
		/* Let's call API */
		$apiURI = 'https://sugapi.verisign-grs.com/ns-api/2.0/suggest?include-registered=false&tlds='.$domainExt.'&include-suggestion-type=true&sensitive-content-filter='.$sn.'&use-numbers='.$inb.'&max-length='.$domainLength.'&lang='.$lang.'&max-results='.$generatorLimit.'&name='.$domain_keyword.'&use-idns=false';
		
		
		$data = file_get_contents( $apiURI );
		$data = json_decode( $data, true );
		
		if( isset( $data['results'] ) && count( $data['results'] ) > 0 )
		{
			
			/* List Domain Names */
			$domainData = '';
			foreach( $data['results'] as $d )
			{
				
				if( $d['availability'] === 'available' )
				{
					$border_color = 'border-success';
					$icon = 'bi-check-circle-fill';
					$text_color = 'text-success';
					$regButton = '<a href="#!" class="btn btn-sm btn-success d-block rounded-1 shadow-sm">Register</a>';
				}else{
					$border_color = 'border-danger';
					$icon = 'bi-x-circle-fill';
					$text_color = 'text-danger';
					$regButton = '<button disabled="" href="#!" class="btn btn-sm btn-outline-danger d-block">Already Registered</button>';
				}
				
				$domainData .= '
					<div class="clearfix p-3 mb-3 rounded-3 shadow '.$border_color.' border-start border-5">
						<div class="float-start">
							<h3 class="fw-bold fs-3 d-block '.$text_color.'"><i class="bi '.$icon.'"></i> '.$d['name'].'</h2>
							<div class="d-flex">
								
								<small class="me-1 text-secondary">'.ucwords( $d['sldSuggestionType'] ).' •</small>
								<small class="me-1 text-secondary">'.ucwords( $d['tldRankingType'] ).'</small>
								<small class="text-secondary">Length: '.strlen( explode(".", $d['name'])[0] ).'</small>
							</div>
						</div>
						<div class="float-end">
							<div class="text-center">
								'.$regButton.'
								<small class="d-block mt-1 text-secondary">'.ucwords( $d['availability'] ).'</small>
							</div>
						</div>
					</div>
				';
				
			}
			
			$html = '
				<div class="p-3">
					<h2 class="fw-bold fs-2 text-center text-primary">Generated Domain Names</h2>
					<div class="mt-3">
						'.$domainData.'
					</div>
				</div>
			';
			
			$output = array(
			
				'status' => 'success',
				'message' => 'We have generated '.count( $data['results'] ).' domain names for you!',
				'data' => $html,
				'apiURI' => $apiURI,
			);
			
		}else{
			
			$output = array(
			
				'status' => 'error',
				'message' => 'Failed to generate domain names at this moment, please try again later.',
				'data' => null,
			);
			
		}
		
	}else{
			$output = array(
			
				'status' => 'error',
				'message' => 'Something is wrong...',
				'data' => null,
			);
	}
	
	echo json_encode( $output );