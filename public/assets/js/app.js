(function(){
	'use strict';
	function onlyDigits(value){ return (value||'').replace(/\D+/g, ''); }
	
	window.applyCpfMask = function(input){
		if(!input) return;
		input.addEventListener('input', function(){
			let v = onlyDigits(input.value).slice(0,11);
			let p1=v.slice(0,3), p2=v.slice(3,6), p3=v.slice(6,9), p4=v.slice(9,11);
			let out='';
			if(p1) out += p1;
			if(p2) out += '.'+p2;
			if(p3) out += '.'+p3;
			if(p4) out += '-'+p4;
			input.value = out;
		});
	};
	
	window.applyCnpjMask = function(input){
		if(!input) return;
		input.addEventListener('input', function(){
			let v = onlyDigits(input.value).slice(0,14);
			let p1=v.slice(0,2), p2=v.slice(2,5), p3=v.slice(5,8), p4=v.slice(8,12), p5=v.slice(12,14);
			let out='';
			if(p1) out += p1;
			if(p2) out += '.'+p2;
			if(p3) out += '.'+p3;
			if(p4) out += '/'+p4;
			if(p5) out += '-'+p5;
			input.value = out;
		});
	};
	
	window.applyPhoneMask = function(input){
		if(!input) return;
		input.addEventListener('input', function(){
			let v = onlyDigits(input.value).slice(0,11);
			let out='';
			if(v.length <= 10) {
				// (XX) XXXX-XXXX
				let p1=v.slice(0,2), p2=v.slice(2,6), p3=v.slice(6,10);
				if(p1) out += '('+p1;
				if(p2) out += ') '+p2;
				if(p3) out += '-'+p3;
			} else {
				// (XX) XXXXX-XXXX
				let p1=v.slice(0,2), p2=v.slice(2,7), p3=v.slice(7,11);
				if(p1) out += '('+p1;
				if(p2) out += ') '+p2;
				if(p3) out += '-'+p3;
			}
			input.value = out;
		});
	};
})();
