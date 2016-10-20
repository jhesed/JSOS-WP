<?php

/**
 * Class SocialSharing_GridGallery_Module
 * interaction with Grid Gallery plugin
 */
class SocialSharing_GridGallery_Module extends SocialSharing_Core_Module
{
	private $gridGalleryClass;
	public function onInit()
	{
		parent::onInit();
		add_filter('sss_get_projects_list', array($this, 'getProjectsList'), 10, 0);
		add_filter('sss_gallery_html', array($this, 'injectProjectHtml'), 10, 1);
		add_action('sss_show_at_grid_gallery',array($this,'showAtGridGallery'), 10,1);
		$this->gridGalleryClass = 'SupsysticGallery';
	}

	/**
	 * change social sharing project settings to show it in grid gallery
	 * @param int $projectId social sharing project id
  	*/
	public function showAtGridGallery($projectId){
		/**
		 * @var SocialSharing_Projects_Module $projectsModule
		 * @var SocialSharing_Projects_Model_Projects $projectsModel
		 */
		$projectsModule = $this->getEnvironment()->getModule('projects');
		$projectsModel = $projectsModule->getModelsFactory()->get('projects');
		$project = $projectsModel->get($projectId);

		$project->settings['where_to_show'] = 'grid_gallery';
		$projectsModel->save($project->id,$project->settings);
	}

	/**
	 * disable social sharing on galleries that use it
	 * @param $socialSharingProjectId
	 */
	public function disableSocialSharing($socialSharingProjectId){
		do_action('sgg_disable_social_sharing',$socialSharingProjectId);
	}
	
	/**
	 * clean gallery cache that use social sharing project
	 * @param $projectId
	 */
	public function cleanSocialSharingCache($projectId){
		do_action('sgg_clean_cache',$projectId);
	}
	
	/**
	 * check is gallery is intalled
	 * @return bool true if gallery installed and false otherwise
	 */
	public function isInstalled(){
		return class_exists($this->gridGalleryClass);
	}

	/**
	 * Get HTML of social sharing project
	 * @param int $project_id social sharing project id
	 * @return string HTML of social sharing project
	 */
	public function injectProjectHtml($project_id)
	{
		$project = $this->getProjectsModel()->get($project_id);

		if (null === $project) {
			echo 'project is null';
			return '';
		}

		return $this->getProjectsModule()->doShortcode(array(
			'id' => $project->id,
//			'place' => 'grid_gallery',
//			'extra' => 'left',
		));
	}

	/**
	 * Get list of social share projects
	 * @return array of pairs where key is project id and value is project title
	 */
	public function getProjectsList(){
		$allProjects = $this->getProjectsModel()->all();
		$projectsList = array();
		foreach($allProjects as $project)
			$projectsList[$project->id] = $project->title;
		return $projectsList;
	}

	/**
	 * get the social share projects module
	 * @return SocialSharing_Projects_Module
	 */
	protected function getProjectsModule()
	{
		$resolver = $this->getEnvironment()->getResolver();

		return $resolver->getModulesList()->get('projects');
	}

	/**
	 * get the social share projects model
	 * @return SocialSharing_Projects_Model_Projects
	 */
	protected function getProjectsModel(){
		return $this->getProjectsModule()->getModelsFactory()->get('projects');
	}
}