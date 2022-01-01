<div class="backdrop_box <?php echo isset($medium_backdrop) && $medium_backdrop == 1 ? 'medium' : 'large'; ?>" id="reportissue_container">
    <div class="backdrop_detail_header">
        <span><?php echo __('Report an issue'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="TBG.Main.Helpers.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <?php 
            $params = array();
            if (isset($selected_project))
            {
                $params['selected_project'] = $selected_project;
            }
            if (isset($issue))
            {
                $params['issue'] = $issue;
            }
            if (isset($issuetypes))
            {
                $params['issuetypes'] = $issuetypes;
            }
            if (isset($selected_issuetype))
            {
                $params['selected_issuetype'] = $selected_issuetype;
            }
            if (isset($selected_milestone))
            {
                $params['selected_milestone'] = $selected_milestone;
            }
            if (isset($selected_build))
            {
                $params['selected_build'] = $selected_build;
            }
            if (isset($parent_issue))
            {
                $params['parent_issue'] = $parent_issue;
            }
            if (isset($errors))
            {
                $params['errors'] = $errors;
            }
            if (isset($permission_errors))
            {
                $params['permission_errors'] = $permission_errors;
            }
            if (isset($board))
            {
                $params['board'] = $board;
            }
            if (isset($locked_issuetype))
            {
                $params['locked_issuetype'] = $locked_issuetype;
            }
            include_component('main/reportissue', $params);
        ?>
    </div>
</div>
