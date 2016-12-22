<?php
/**
 * @author Tom Needham
 * @copyright 2016 Tom Needham tom@owncloud.com
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace Tests\Settings\Panels\Admin;

use OC\Settings\Panels\Admin\Certificates;

/**
 * @package Tests\Settings\Panels\Admin
 */
class CertificatesTest extends \Test\TestCase {

	/** @var \OC\Settings\Panels\Admin\Certificates */
	private $panel;

	private $urlGenerator;

	private $config;

	private $certManager;

	public function setUp() {
		parent::setUp();
		$this->urlGenerator =$this->getMockBuilder('\OCP\IURLGenerator')->getMock();
		$this->config = $this->getMockBuilder('\OCP\IConfig')->getMock();
		$this->certManager = $this->getMockBuilder('\OCP\ICertificateManager')->getMock();
		$this->panel = new Certificates($this->config, $this->urlGenerator, $this->certManager);
	}

	public function testGetSection() {
		$this->assertEquals('general', $this->panel->getSectionID());
	}

	public function testGetPriority() {
		$this->assertTrue(is_integer($this->panel->getPriority()));
		$this->assertTrue($this->panel->getPriority() > -100);
		$this->assertTrue($this->panel->getPriority() < 100);
	}

	public function testGetPanel() {
		$mockCert = $this->getMockBuilder('\OCP\ICertificate')->getMock();
		$mockCert->expects($this->once())->method('isExpired')->willReturn(false);
		$mockCert->expects($this->once())->method('getCommonName')->willReturn('commonanme');
		$mockCert->expects($this->exactly(2))->method('getExpireDate')->willReturn(time()+60*60*24*10);
		$mockCert->expects($this->once())->method('getIssuerOrganization')->willReturn('issueOrg');
		$mockCert->expects($this->once())->method('getIssuerName')->willReturn('issuer');
		$mockCert->expects($this->once())->method('getOrganization')->willReturn('org');
		$this->certManager->expects($this->once())->method('listCertificates')->willReturn([$mockCert]);
		$this->urlGenerator->expects($this->once())->method('linkToRoute');
		$templateHtml = $this->panel->getPanel()->fetchPage();
		$this->assertContains('issueOrg', $templateHtml);
		$this->assertContains('issuer', $templateHtml);
		$this->assertContains('commonname', $templateHtml);
		$this->assertContains('org', $templateHtml);
	}

}
