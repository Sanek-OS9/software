<?php
namespace Models;

class Sitemap{
    private $test = false;
    public function __construct()
    {
        $this->xml = new \DomDocument('1.0','utf-8');
        $this->urlset = $this->xml->appendChild($this->xml->createElement('urlset'));
        $this->setAttribute();
    }
    private function setAttribute()
    {
        $this->urlset->setAttribute('xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
        $this->urlset->setAttribute('xsi:schemaLocation','http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
        $this->urlset->setAttribute('xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');
    }
    public function setLinks(array $links)
    {
        for ($i = 0; $i < count($links); $i++) {
            $url = $this->urlset->appendChild($this->xml->createElement('url'));
            $loc = $url->appendChild($this->xml->createElement('loc'));
            $changefreq = $url->appendChild($this->xml->createElement('changefreq'));
            $priority = $url->appendChild($this->xml->createElement('priority'));
            $loc->appendChild($this->xml->createTextNode('http://' . $_SERVER['SERVER_NAME'] . $links[$i]));
            $changefreq->appendChild($this->xml->createTextNode('monthly'));
            $priority->appendChild($this->xml->createTextNode('0.7'));
        }
    }
    public function save()
    {
        $sitemap_path = H . '/Static/sitemap.xml';
        $this->xml->formatOutput = true;
        $this->xml->save($sitemap_path);
        chmod($sitemap_path, 0777);
    }
}
