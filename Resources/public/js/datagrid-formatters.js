jQuery.extend(jQuery.fn.fmatter , {
    datetimeFormatter : function(cellvalue, options, rowdata) {
    	if(null !== cellvalue){
            return cellvalue.date;
    	}
    	
    	return '---';
       
    }
});
