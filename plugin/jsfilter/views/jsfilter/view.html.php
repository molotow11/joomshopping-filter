<?php
/**
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

 use \Joomla\Component\Jshopping\Site\Helper;

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class joomlacomponentjshoppingsitecontrollersearchViewjsfilter extends JViewLegacy {

	function display($tpl = null) {	
				$mainframe = JFactory::getApplication();
				JSFactory::loadLanguageFile();
				$jshopConfig = JSFactory::getConfig();

				require_once (JPATH_SITE.DS.'modules'.DS.'mod_jshopping_extended_filter'.DS.'helper.php');
				$moduleId = \JFactory::getApplication()->input->get("moduleId");
				$moduleParams = modJShopExtendedFilterHelper::getModuleParams($moduleId);			
				
				if (!defined('JPATH_ROOT')) {
				   define('JPATH_ROOT', JPath::clean(JPATH_SITE));
				}
	
				$pluginPath = JPATH_BASE.DS.'plugins'.DS.'system'.DS.'jsfilter'.DS.'jsfilter';
	
				require_once($pluginPath.DS.'models'.DS.'search.php');
				
				// JShopping worksheet
				
					require_once(JPATH_COMPONENT.DS.'Helper'.DS.'Helper.php');
					
					$results = ExtendedFilterModel::getResults(); 
					
					$results = \JSHelper::listProductUpdateData($results);
					\JSHelper::addLinkToProducts($results, 0, 1);
				
					// pagination
						jimport('joomla.html.pagination');
						
						$products_page = $jshopConfig->count_products_to_page;
						$context = "jshoping.alllist.front.product";
						
						$limit = JFactory::getApplication()->input->get("limit");
						if (!$limit) $limit = $products_page;
						$limitstart = JFactory::getApplication()->input->get('limitstart');
						
						$results_total = ExtendedFilterModel::getResults(true);
					   
						$pagination = new JPagination($results_total, $limitstart, $limit);
						$pagenav = $pagination->getPagesLinks();

					//
					
					// ordering & limits				
					
						$orderby = JFactory::getApplication()->input->get("orderby", "");
						if($orderby == "") {
							$orderby = $jshopConfig->product_sorting;
						}
						
						$orderto = JFactory::getApplication()->input->get("orderto", "");
						if($orderto == "") {
							$orderto = $jshopConfig->product_sorting_direction;
						}

						if($orderto == "1") {
							$order_to_img = 'arrow_down.gif';
						}
						else {
							$order_to_img = 'arrow_up.gif';
						}
						
						foreach ($jshopConfig->sorting_products_name_select as $key=>$value) {
							$sorts[] = JHTML::_('select.option', $key, $value, 'sort_id', 'sort_value');
						}
						$order_select = JHTML::_('select.genericlist', $sorts, '', 'class = "inputbox" size = "1" onchange = "document.ExtendedFilter'.$moduleId.'.orderby.value=this.value; submit_form_'.$moduleId.'()"','sort_id', 'sort_value', $orderby);
						
						\JSHelper::insertValueInArray($jshopConfig->count_products_to_page, $jshopConfig->count_product_select); //insert category count
						foreach ($jshopConfig->count_product_select as $key=>$value) {
							$product_count[] = JHTML::_('select.option', $key, $value, 'count_id', 'count_value' );
						}
						$limit_select = JHTML::_('select.genericlist', $product_count, '', 'class = "inputbox" size = "1" onchange = "document.ExtendedFilter'.$moduleId.'.limit.value=this.value; submit_form_'.$moduleId.'()"','count_id', 'count_value', $limit );
					
					//
					
					\JFactory::getApplication()->triggerEvent('onBeforeDisplayProductList', array(&$results) );
					
					$this->set('pagenav', $pagenav);
					$this->set('results_total', $results_total);
					
					$this->set('order_select', $order_select);
					$this->set('order_to_img', $jshopConfig->live_path.'images/'.$order_to_img);
					$this->set('orderto', $orderto);
					$this->set('limit_select', $limit_select);
				
					$this->set('results', $results);
					
				//
						
				$this->addTemplatePath($pluginPath.DS.'templates');
				$this->addTemplatePath($pluginPath.DS.'templates'.DS.$jshopConfig->template);
				
				// Look for template files in component folders
				$this->addTemplatePath(JPATH_COMPONENT.DS.'templates');
				$this->addTemplatePath(JPATH_COMPONENT.DS.'templates'.DS.$jshopConfig->template);

				// Look for overrides in template folder (Joomshopping template structure)
				$this->addTemplatePath(JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_jshopping'.DS.'templates');
				$this->addTemplatePath(JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_jshopping'.DS.'templates'.DS.$jshopConfig->template);

				// Look for overrides in template folder (Joomla! template structure)
				$this->addTemplatePath(JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_jshopping'.DS.$jshopConfig->template);
				$this->addTemplatePath(JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_jshopping');
				
				$results_template = $moduleParams->results_template;

				$productlist = \JSFactory::getModel('product', 'Site\\Productlist');
        $productlist->load();
				$this->set("count_product_to_row", $productlist->getCountProductsToRow());
					
				if(!$results_total) {
					$this->setLayout("noresult");
				}
				else {
					$this->setLayout("products");
				}
				
				if($results_template == "category") {
					$restmode = $moduleParams->restmode;
					if($restmode == 0) {
						$restcat = $moduleParams->restcat;
					}
					else {
						$restcat = JFactory::getApplication()->input->get("restcata", '');
					}
					if($restcat == '') {
						$restcat = JFactory::getApplication()->input->get("category");
						$restcat = $restcat[0];
					}
					if($restcat == '') {
						$restcat = JFactory::getApplication()->input->get("category_id", 1);
					}
					
					$user = JFactory::getUser();
					$category_id = $restcat;
					
					$category = JSFactory::getTable('category', 'jshop');
					$category->load($category_id);
					$category->getDescription();
					\JFactory::getApplication()->triggerEvent('onAfterLoadCategory', array(&$category, &$user));

					if (!$category->checkView($user)){
						JError::raiseError(404, _JSHOP_PAGE_NOT_FOUND);
						return;
					}
					
					$sub_categories = $category->getChildCategories($category->getFieldListOrdering(), $category->getSortingDirection(), 1);
					\JFactory::getApplication()->triggerEvent('onBeforeDisplayCategory', array(&$category, &$sub_categories) );
					
					JshopHelpersMetadata::category($category);
					
					$productlist = JSFactory::getModel('productList', 'jshop');
					$productlist->setModel($category);
					$productlist->load();
					
					//category variables
					$display_list_products = count($results) > 0;
					
					//assignments
					$this->set('results_template', $results_template);
					$this->set('config', $jshopConfig);
					$this->set('template_block_list_product', $productlist->getTmplBlockListProduct());
					$this->set('template_no_list_product', $productlist->getTmplNoListProduct());
					$this->set('template_block_form_filter', $productlist->getTmplBlockFormFilter());
					$this->set('template_block_pagination', $productlist->getTmplBlockPagination());
					$this->set('path_image_sorting_dir', $jshopConfig->live_path.'images/'.$order_to_img);
					$this->set('filter_show', 1);
					$this->set('filter_show_category', 0);
					$this->set('filter_show_manufacturer', 1);
					$this->set('pagination', $pagenav);
					$this->set('pagination_obj', $pagination);
					$this->set('display_pagination', $pagenav!="");
					$this->set('rows', $results);
					$this->set('count_product_to_row', $productlist->getCountProductsToRow());
					$this->set('image_category_path', $jshopConfig->image_category_live_path);
					$this->set('noimage', $jshopConfig->noimage);
					$this->set('category', $category);
					$this->set('product_count', $limit_select);
					$this->set('sorting', $order_select);
					$this->set('display_list_products', $display_list_products);
					$this->set('shippinginfo', SEFLink('index.php?option=com_jshopping&controller=content&task=view&page=shipping',1));
					
					$this->set('action', $productlist->getAction());
					$this->set('orderby', $productlist->getOrderBy());
					$this->set('manufacuturers_sel', $productlist->getHtmlSelectFilterManufacturer());
					$this->set('filters', $productlist->getFilters());
					$this->set('willBeUseFilter', $productlist->getWillBeUseFilter());		
					$this->set('categories', $sub_categories);
					$this->set('count_category_to_row', $category->getCountToRow());
					$this->set('allow_review', $productlist->getAllowReview());
					$this->set('total', $results_total);
					///
					
					// extra code for fix ordering direction switcher
					$document = JFactory::getDocument();
					$script = "
						function submitListProductFilterSortDirection() {
							return false;
						}
					
						jQuery('document').ready(function() {
							jQuery('.jshop_list_product .box_products_sorting img').click(function() {
								var orderto = jQuery('#ExtendedFilterContainer".$moduleId." input[name=orderto]');
								if(orderto.val() == undefined || orderto.val() == '') {
									orderto.val(".$jshopConfig->product_sorting_direction.");
								}
								orderto.val(orderto.val() ^ 1);
								submit_form_".$moduleId."();
							});
						});
					";
					$document->addScriptDeclaration($script);	
					//
					
					if ($category->category_template == "") { 
						$category->category_template = "default";
					}
					
					$this->addTemplatePath(JPATH_COMPONENT.DS.'templates'.DS.$jshopConfig->template.DS.'category');
					
					// Look for overrides in template folder (Joomshopping template structure)
					$this->addTemplatePath(JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_jshopping'.DS.'templates'.DS.'category');
					$this->addTemplatePath(JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_jshopping'.DS.'templates'.DS.$jshopConfig->template.DS.'category');

					// Look for overrides in template folder (Joomla! template structure)
					$this->addTemplatePath(JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_jshopping'.DS.$jshopConfig->template.DS.'category');
					$this->addTemplatePath(JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_jshopping'.DS.'category');
					
					$this->setLayout("category_" . $category->category_template);
				}

				parent::display($tpl);
	}

}

?>