<?php
if (!preg_match("/wakka.php/", $_SERVER['PHP_SELF'])) {
    die ("acc&egrave;s direct interdit");
}
echo $this->Header();
?>
<div class="page">
<?php
if ($this->HasAccess("read")) {


/**
 * Clone the current page and save a copy of it as a new page.
 *
 * Usage: append /clone to the URL of the page you want to clone
 *
 * This handler checks the existence of the source page, the validity of the
 * name of the target page to be created, the user's read-access to the source
 * page and write-access to the target page.
 * If the edit option is selected, the user is redirected to the target page for
 * edition immediately after its creation.
 *
 * @package         Handlers
 * @subpackage       
 * @name              clone
 *
 * @author            {@link http://wikkawiki.org/ChristianBarthelemy Christian Barthelemy} - original idea and code.
 * @author            {@link http://wikkawiki.org/DarTar Dario Taraborelli} - bugs fixed, code improved, removed popup alerts. 
 * @version           0.4
 * @since             Wikka 1.1.6.0
 *                     
 * @input             string  $to  required: the page to be created
 *                            must be a non existing page and current user must be authorized to create it
 *                            default is source page name                
 *
 * @input             string  $note  optional: the note to be added to the page when created
 *                            default is "Cloned from " followed by the name of the source page
 *
 * @input             boolean $editoption optional: if true, the new page will be opened for edition on creation
 *                            default is false (to allow multiple cloning of the same source)
 *
 * @todo              Use central library for valid pagenames.
 *       
 */
// defaults
if(!defined('VALID_PAGENAME_PATTERN')) define ('VALID_PAGENAME_PATTERN', '/^[A-Za-zÄÖÜßäöü]+[A-Za-z0-9ÄÖÜßäöü]*$/s');

// i18n
define('CLONE_HEADER', '==== Cloner cette page ====');
define('CLONE_SUCCESSFUL', '%s est clon&eacute;e!');
define('CLONE_X_TO', 'Cloner %s en:');
define('CLONED_FROM', 'Clon&eacute;e depuis %s');
define('EDIT_NOTE', 'Edit note:');
define('ERROR_ACL_READ', 'You are not allowed to read the source of this page.');
define('ERROR_ACL_WRITE', 'Vous n\'avez pas les permissions sur %s!');
define('ERROR_INVALID_PAGENAME', 'This page name is invalid. Valid page names must start with a letter and contain only letters and numbers.');
define('ERROR_PAGE_ALREADY_EXIST', 'La page destination existe d&eacute;j&agrave;!');
define('ERROR_PAGE_NOT_EXIST', ' Sorry, page %s does not exist.');
define('LABEL_CLONE', 'Cloner');
define('LABEL_EDIT_OPTION', ' Editer apr&egrave;s la cr&eacute;ation ');
define('LABEL_CLONEACLS_OPTION', ' Clone ACLs ');
define('PLEASE_FILL_VALID_TARGET', 'Compl&eacute;ter ce formulaire.');

// initialization
$from = $this->tag;
$to = $this->tag;
//$note = sprintf(CLONED_FROM, $from);
$editoption = '';
$cloneacls = '';
$box = PLEASE_FILL_VALID_TARGET;

// print header
echo $this->Format(CLONE_HEADER);
echo "<br />";
// 1. check source page existence
if (!$this->ExistsPage($from))
{
    // source page does not exist!
    $box = sprintf(ERROR_PAGE_NOT_EXIST, $from);
} else
{
    // 2. page exists - now check user's read-access to the source page
    if (!$this->HasAccess('read', $from))
    {
        // user can't read source page!
        $box = ERROR_ACL_READ;
    } else
    {
        // page exists and user has read-access to the source - proceed
        if (isset($_POST) && $_POST)
        {
            // get parameters
            $to = isset($_POST['to']) && $_POST['to'] ? $_POST['to'] : $to;
            //$note = isset($_POST['note']) ? $_POST['note'] : $note;
            $editoption = (isset($_POST['editoption']))? 'checked="checked"' : '';
            $cloneacls = (isset($_POST['cloneacls']))? 'checked="checked"' : '';
       
            // 3. check target pagename validity
            if (!preg_match(VALID_PAGENAME_PATTERN, $to))  //TODO use central regex library
            {
                // invalid pagename!
                $box = '""<em class="error">'.ERROR_INVALID_PAGENAME.'</em>""';
            } else
            {
                // 4. target page name is valid - now check user's write-access
                if (!$this->HasAccess('write', $to)) 
                {
                    $box = '""<em class="error">'.sprintf(ERROR_ACL_WRITE, $to).'</em>""';
                } else
                {
                    // 5. check target page existence
                    if ($this->ExistsPage($to))
                    {
                        // page already exists!
                        $box = '""<em class="error">'.ERROR_PAGE_ALREADY_EXIST.'</em>""';
                    } else
                    {
                        // 6. Valid request - proceed to page cloning
                        $thepage=$this->LoadPage($from); # load the source page
                        if ($thepage) $pagecontent = $thepage['body']; # get its content
                        $this->SavePage($to, $pagecontent, $note); #create target page
                        if ($cloneacls == 'checked="checked"')
                        {
                            // Clone ACLs too
                            $acls = $this->LoadAllACLs($from);
                            $this->SaveACL($to, 'read', $acls['read_acl']);
                            $this->SaveACL($to, 'write', $acls['write_acl']);
                            $this->SaveACL($to, 'comment', $acls['comment_acl']);
                        }
                        if ($editoption == 'checked="checked"')
                        {
                            // quick edit
                            $this->Redirect($this->href('edit', $to));
                        } else
                        {
                            // show confirmation message
                            $box = '""<em class="success">'.sprintf(CLONE_SUCCESSFUL, $to).'</em>""';
                        }
                    }
                }
            }
        }
        // build form
        $form = $this->FormOpen('clone');
        $form .= '<table class="clone">'."\n".
            '<tr>'."\n".
            '<td>'.sprintf(CLONE_X_TO, $this->Link($this->GetPageTag())).'</td>'."\n".
            '<td><input type="text" name="to" value="'.$to.'" size="37" maxlength="75" /></td>'."\n".
            '</tr>'."\n".
            '<tr>'."\n".
            '<td></td>'."\n".
            '<td>'."\n".
            '<input type="checkbox" name="editoption" '.$editoption.' id="editoption" /><label for="editoption">'.LABEL_EDIT_OPTION.'</label><br />'."\n".
            '<br /><input type="submit" name="create" value="'.LABEL_CLONE.'" />'."\n".
            '</td>'."\n".
            '</tr>'."\n".
            '</table>'."\n";
        $form .= $this->FormClose();
    }
}

// display messages
if (isset($box)) echo $this->Format(' --- '.$box.' --- --- ');
// print form
if (isset($form)) print $form;

} 

else
{
	echo "<i>Vous n'avez pas acc&egrave;s &agrave; cette page.</i>" ;
}
?>
</div>

<?php echo $this->Footer(); ?>
