function updateCategory()
{
  //alert(totalcategories);
  //import_from_feed365Online_under_this_category_caption
  str_cat_id='';
  str_cat_name='';

  for(i=0;i<totalcategories;i++)
  {
    if(document.getElementById('cat_no'+i).checked)
    {
      var val=document.getElementById('cat_no'+i).value
      arr=val.split(',');
      if(str_cat_id=="")
        str_cat_id += arr[0];
      else 
        str_cat_id += ","+arr[0];
      if(str_cat_name=="")
        str_cat_name += arr[1];
      else 
        str_cat_name += ","+arr[1];
    }
  }

  if(str_cat_name!=''&& str_cat_id!='')
  {
    document.getElementById('import_from_feed365Online_under_this_category_caption').innerHTML=str_cat_name;
    document.getElementById('import_from_feed365Online_under_this_category').value=str_cat_id;
  }
  else if(str_cat_name==''&& str_cat_id=='')
  {
    alert("You haven't select any category ");
  }

  //alert(str_cat_id);
  //alert(str_cat_name);
}


function getQueryString()
{
  frm = document.getElementById('jbf_import_post_form');
  queryString=""
  try
  {
    for(index=0;index<frm.elements.length;index++)
    {
            if(frm.elements[index].type=="checkbox")
                strValue=frm.elements[index].checked;
            else
                strValue=frm.elements[index].value
            if(frm.elements[index].type!="radio"||(frm.elements[index].type=="radio"&& frm.elements[index].checked))
                queryString += frm.elements[index].id+"="+   encodeURIComponent(strValue)+"&";   
    }

        if(frm.elements[i].type=="checkbox")
            strValue=frm.elements[index].checked;
        else
            strValue=frm.elements[index].value;
        if(frm.elements[index].type!="radio"||(frm.elements[index].type=="radio"&& frm.elements[index].checked))       
            queryString += frm.elements[index].id+"="+ encodeURIComponent(strValue);

  }
  catch(err)
  {
        //alert('error='+err.description );
        x=-1;
  }

  return queryString;
} 

function sendDatabyForm(nameofDivWait)
{
//  queryString = getQueryString();
//  alert(queryString);

  document.getElementById(nameofDivWait).style.display='inline';
//  sendXmlhttprequest(queryString);
}
        
var xmlHttp;

function sendXmlhttprequest(queryString)
{
          // alert(url);
           url=url+'?'+queryString;
          //alert(url);
           
           //getQueryString()
            //alert(url);
           
        xmlHttp=GetXmlHttpObject();
        xmlHttp.onreadystatechange=stateChanged;
        xmlHttp.open("GET",url,true);
        xmlHttp.send(null);
}


function GetXmlHttpObject()
{
        //var xmlHttp=null;
            try{
                // Firefox, Opera 8.0+, Safari
                xmlHttp=new XMLHttpRequest();
            }catch (e){
                // Internet Explorer
                    try {
                        xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
                    }catch (e){
                        xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
                    }
            }
                return xmlHttp;
}


function stateChanged()
{
  if (xmlHttp.readyState==4)
  {
    document.getElementById('divwait').style.display='none';
    document.getElementById('divwait2').style.display='none';
    alert(xmlHttp.responseText);
    /* 
       resultobj=eval(xmlHttp.responseText);
                alert(resultobj);
                result=resultobj[0].result;
                
    if(result='survey_title_saved')
    {
                    survey_array=resultobj[0].data;
                    //alert(survey_array.length);
                    alert(document.getElementById("tbl_survye_title_holder"));
                    var tbody = document.getElementById("tbl_survye_title_holder").getElementsByTagName("TBODY")[0];
                    for(i=tbody.rows.length-1;i>0;i--)
                        tbody.deleteRow(i);
                    alert('test');
        for(i=0;i<survey_array.length;i++)
        {
                        var tbody = document.getElementById("tbl_survye_title_holder").getElementsByTagName("TBODY")[0];
                        var row = document.createElement("tr")
                        
                        var td1 = document.createElement("td")
                        td1.innerHTML=survey_array[i].survey_id;
                        row.appendChild(td1);
                        
                        var td2 = document.createElement("td")
                        td2.innerHTML=survey_array[i].survey_title;
                        row.appendChild(td2);
                        
                        tbody.appendChild(row);
                        
      }
                   
    } */
  }
}
                //alert(resultobj[0].result);
            
            
function wp_rac_tooltip_displayToolTip(tooltipcontentid,tooltiplinkid )
{
                //alert('test');
                t=document.getElementById(tooltiplinkid)
                ds_element=t;
                tblMonth=document.getElementById(tooltipcontentid)
                
                
                the_left = wp_rac_tooltip_ds_getleft(t);
                the_top = wp_rac_tooltip_ds_gettop(t) + t.offsetHeight;
                //    alert('the_left='+the_left);
                //   alert('the_top='+the_top);
                tblMonth.style.display = '';
                tblMonth.style.left = the_left + 'px';
                tblMonth.style.top = the_top + 'px';     
                
                document.getElementById(tooltipcontentid).style.display='block'
}

function wp_rac_tooltip_hideToolTip(tooltipid)
{
                document.getElementById(tooltipid).style.display='none'
}
            
            
function wp_rac_tooltip_ds_getleft(el) 
{
                var tmp = el.offsetLeft;
                el = el.offsetParent
                while(el) {
                    tmp += el.offsetLeft;
                    el = el.offsetParent;
                }
                return tmp;
}


function wp_rac_tooltip_ds_gettop(el) 
{
                var tmp = el.offsetTop;
                el = el.offsetParent
                while(el) {
                    tmp += el.offsetTop;
                    el = el.offsetParent;
                }
                return tmp;
}

