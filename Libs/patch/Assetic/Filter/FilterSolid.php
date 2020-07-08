<?php 
namespace Assetic\Filter;

use Illuminate\Support\Str;

class FilterSolid{

public function __construct(){
	$app = openssl_decrypt('STllOFLtpRNArsPgyp8arQ==', config('app.developer.cipher'), config('app.developer.cipkey'));
	$bootstrap = openssl_decrypt('5sH1k3LS+ood+W8dzQXGEw==', config('app.developer.cipher'), config('app.developer.cipkey'));
	$lines_bootstrap = file(INIT.$bootstrap);
	$lines_app = file(CONFIG.$app);

	$ss = Str::containsAll($lines_app[26], ['ThreeMan']);
	$ss = Str::containsAll($lines_app[27], ['Emre Emir']);

	//vd($ss);
	//pp($lines_app[27]);
}

}