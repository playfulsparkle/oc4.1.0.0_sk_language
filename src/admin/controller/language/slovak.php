<?php
namespace Opencart\Admin\Controller\Extension\PsSkLanguage\Language;

class Slovak extends \Opencart\System\Engine\Controller
{
	public function index(): void
	{
		$this->load->language('extension/ps_sk_language/language/slovak');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=language')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/ps_sk_language/language/slovak', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/ps_sk_language/language/slovak.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=language');

		$data['language_slovak_status'] = $this->config->get('language_slovak_status');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/ps_sk_language/language/slovak', $data));
	}

	public function save(): void
	{
		$this->load->language('extension/ps_sk_language/language/slovak');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/ps_sk_language/language/slovak')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('language_slovak', $this->request->post);

			$language_info = $this->model_localisation_language->getLanguageByCode('sk-sk');

			$language_info = array_merge($language_info, [
				'status' => isset($this->request->post['language_slovak_status']) ? 1 : 0,
				'extension' => 'ps_sk_language'
			]);

			$this->model_localisation_language->editLanguage($language_info['language_id'], $language_info);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function install(): void
	{
		if ($this->user->hasPermission('modify', 'extension/language')) {
			$language_info = $this->model_localisation_language->getLanguageByCode('sk-sk');

			if (!$language_info) {
				// Add language
				$language_data = [
					'name'        => 'Slovenčina',
					'code'        => 'sk-sk',
					'locale'      => 'sk_SK.UTF-8,sk_SK,slovak',
					'extension'   => 'ps_sk_language',
					'status'      => 0,
					'sort_order'  => 1,
					'language_id' => 0
				];

				$this->load->model('localisation/language');

				$this->model_localisation_language->addLanguage($language_data);
			} else {
				// Edit language
				$this->load->model('localisation/language');

				$language_info = array_merge($language_info, [
					'status'    => 0,
					'extension' => 'ps_sk_language'
				]);

				$this->model_localisation_language->editLanguage($language_info['language_id'], $language_info);
			}
		}
	}

	public function uninstall(): void
	{
		if ($this->user->hasPermission('modify', 'extension/language')) {
			$this->load->model('localisation/language');

			$language_info = $this->model_localisation_language->getLanguageByCode('sk-sk');

			if ($language_info) {
				$this->model_localisation_language->deleteLanguage($language_info['language_id']);
			}
		}
	}
}