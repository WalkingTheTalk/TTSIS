<?php
#**************************************************************************
#  openSIS is a free student information system for public and non-public 
#  schools from Open Solutions for Education, Inc. web: www.os4ed.com
#
#  openSIS is  web-based, open source, and comes packed with features that 
#  include student demographic info, scheduling, grade book, attendance, 
#  report cards, eligibility, transcripts, parent portal, 
#  student portal and more.   
#
#  Visit the openSIS web site at http://www.opensis.com to learn more.
#  If you have question regarding this system or the license, please send 
#  an email to info@os4ed.com.
#
#  This program is released under the terms of the GNU General Public License as  
#  published by the Free Software Foundation, version 2 of the License. 
#  See license.txt.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#***************************************************************************************
include('../../RedirectModulesInc.php');
if($_REQUEST['modfunc']=='save')
{
	if(count($_REQUEST['st_arr']))
	{
	$st_list = '\''.implode('\',\'',$_REQUEST['st_arr']).'\'';
	$extra['WHERE'] = ' AND s.STUDENT_ID IN ('.$st_list.')';

        
        
        $extra['FROM']=' ,students_join_people sjp,people p,student_address sa';
        $extra['SELECT']=' ,sjp.EMERGENCY_TYPE AS CONTACT_TYPE,sjp.RELATIONSHIP AS RELATION,CONCAT(p.Last_Name," " ,p.First_Name) AS RELATION_NAME,sa.STREET_ADDRESS_2 as STREET,sa.STREET_ADDRESS_1 as ADDRESS,sa.CITY,sa.STATE,sa.ZIPCODE AS ZIP,p.WORK_PHONE,p.HOME_PHONE,p.CELL_PHONE,p.EMAIL AS EMAIL_ID';
        $extra['WHERE'] .=' AND sjp.student_id=ssm.student_id AND sjp.STUDENT_ID=sa.STUDENT_ID AND sjp.PERSON_ID=sa.PEOPLE_ID AND sjp.PERSON_ID=p.STAFF_ID';
        $extra['ORDER'] =' ,sa.ID';

                $RET = GetStuList($extra);
  
	if(count($RET))
	{
                        $column_name=array('STUDENT_ID'=>'Student ID','ALT_ID'=>'Alternate ID','FULL_NAME'=>'Student','CONTACT_TYPE'=>'Type','RELATION'=>'Relation','RELATION_NAME'=>'Relation\'s Name','STREET'=>'Street','ADDRESS'=>'Address','CITY'=>'City','STATE'=>'State','ZIP'=>'Zip','WORK_PHONE'=>'Work Phone','HOME_PHONE'=>'Home Phone','CELL_PHONE'=>'Cell Phone','EMAIL_ID'=>'Email Address');
                        $singular='Student Contact';
                        $plural='Student Contacts';
                        $options=array('search' => false);
						echo '<div style="overflow:auto; width:820px; overflow-x:scroll;">';
                        ListOutput($RET, $column_name,$singular,$plural,$link=false,$group=false,$options);
						echo '</div>';
	}
	else{
		ShowErrPhp('No Contacts were found.');
                                    for_error();
                        }
	}
	else{
		ShowErrPhp('You must choose at least one student.');
                                    for_error();
                        }
	unset($_SESSION['student_id']);
	
	$_REQUEST['modfunc']=true;
}

if(!$_REQUEST['modfunc'])
{
	DrawBC("Students >> ".ProgramTitle());

	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=save&include_inactive=$_REQUEST[include_inactive]&_search_all_schools=$_REQUEST[_search_all_schools] method=POST>";
	

	}

	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ',s.STUDENT_ID AS CHECKBOX';
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller checked onclick="checkAll(this.form,this.form.controller.checked,\'st_arr\');"><A>');
	$extra['options']['search'] = false;
	$extra['new'] = true;



	Search('student_id',$extra);
	if($_REQUEST['search_modfunc']=='list')
	{
		echo '<BR><CENTER><INPUT type=submit class=btn_xxlarge value=\'Print Contact Info for Selected Students\'></CENTER>';
		echo "</FORM>";
	}
}

// GetStuList by default translates the grade_id to the grade title which we don't want here.
// One way to avoid this is to provide a translation function for the grade_id so here we
// provide a passthru function just to avoid the translation.

function _makeChooseCheckbox($value,$title)
{
	return '<INPUT type=checkbox name=st_arr[] value='.$value.' checked>';
}
?>
