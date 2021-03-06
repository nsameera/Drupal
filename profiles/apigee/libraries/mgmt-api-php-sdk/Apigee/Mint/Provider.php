<?php
namespace Apigee\Mint;

use Apigee\Exceptions\NotImplementedException;
use Apigee\Util\OrgConfig;

class Provider extends Base\BaseObject
{

    /**
     * @var string
     */
    private $authType;

    /**
     * @var string
     */
    private $credential;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $endpoint;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $merchantCode;

    /**
     * @var string
     */
    private $name;

    /**
     * Class constructor
     * @param \Apigee\Util\OrgConfig $config
     */
    public function __construct(OrgConfig $config)
    {
        $base_url = '/mint/organizations/' . $config->orgName . '/providers';
        $this->init($config, $base_url);
        $this->wrapperTag = 'provider';
        $this->idField = 'id';
        $this->idIsAutogenerated = true;
        $this->initValues();
    }


    /**
     * Implements Base\BaseObject::instantiateNew().
     *
     * @return \Apigee\Mint\Base\BaseObject
     */
    public function instantiateNew()
    {
        return new Provider($this->config);
    }

    /**
     * Implements Base\BaseObject::loadFromRawData().
     *
     * Given an associative array from the raw JSON response, populates the
     * object with that data.
     *
     * @param array $data
     * @param bool $reset
     *
     * @return void
     */
    public function loadFromRawData($data, $reset = false)
    {
        if ($reset) {
            $this->initValues();
        }
        $this->authType = $data['authType'];
        $this->credential = $data['credential'];
        $this->description = $data['description'];
        $this->id = $data['id'];
        $this->merchantCode = $data['merchantCode'];
        $this->name = $data['name'];
    }

    /**
     * Implements Base\BaseObject::initValues().
     *
     * @return mixed
     */
    protected function initValues()
    {
        $this->authType = null;
        $this->credential = null;
        $this->description = null;
        $this->endpoint = null;
        $this->id = null;
        $this->merchantCode = null;
        $this->name = null;
    }

    /**
     * @param null $page_num
     * @param int $page_size
     *
     * @return array
     */
    public function getList($page_num = null, $page_size = 20)
    {
        /*
        $cache_manager = CacheFactory::getCacheManager();
        $data = $cache_manager->get('payment_providers', null);
        if (!isset($data)) {
          $return_objects = parent::getList($page_num, $page_size);
          $data = $this->responseObj;
          $cache_manager->set('payment_providers', $data);
        }
        */
        $return_objects = parent::getList($page_num, $page_size);
        $data = $this->responseObj;
        foreach ($data[$this->wrapperTag] as $response_data) {
            $obj = $this->instantiateNew();
            $obj->loadFromRawData($response_data);
            $return_objects[] = $obj;
        }
        return $return_objects;
    }

    /**
     * @param null $id
     *
     * @throws \Apigee\Exceptions\NotImplementedException
     */
    public function load($id = null)
    {
        throw new NotImplementedException();
    }

    /**
     * @param string $save_method
     *
     * @throws \Apigee\Exceptions\NotImplementedException
     */
    public function save($save_method = 'auto')
    {
        throw new NotImplementedException();
    }

    /**
     * @throws \Apigee\Exceptions\NotImplementedException
     */
    public function delete()
    {
        throw new NotImplementedException();
    }

    /**
     * @param string $authType
     */
    public function setAuthType($authType)
    {
        $this->authType = $authType;
    }

    /**
     * @return string
     */
    public function getAuthType()
    {
        return $this->authType;
    }

    /**
     * @param string $credential
     */
    public function setCredential($credential)
    {
        $this->credential = $credential;
    }

    /**
     * @return string
     */
    public function getCredential()
    {
        return $this->credential;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $endpoint
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $merchantCode
     */
    public function setMerchantCode($merchantCode)
    {
        $this->merchantCode = $merchantCode;
    }

    /**
     * @return string
     */
    public function getMerchantCode()
    {
        return $this->merchantCode;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param int $responseCode
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;
    }

    /**
     * @return int
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * Returns a JSON representation of the object.
     *
     * @return mixed
     */
    public function __toString()
    {
        // TODO: Implement __toString() method.
    }
}
