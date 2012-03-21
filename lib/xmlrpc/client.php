<?php
/*
*   This file is part of CribzLib.
*
*    CribzLib is free software: you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation, either version 3 of the License, or
*    (at your option) any later version.
*
*    CribzLib is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with CribzLib.  If not, see <http://www.gnu.org/licenses/>.
*/
/**
* @package      CribzLib
* @subpackage   Cribz Xmlrpc Client
* @author       Christopher Tombleson
* @copyright    Copyright 2011 onwards
*/
class CribzXmlrpcClient {

    /**
    * Server
    *
    * @var string
    */
    private $server;

    /**
    * Constructor
    * Create a new Xmlrpc Client.
    *
    * @param string $server     XMLRPC Server to send requests to.
    */
    function __construct($server) {
        $this->server = $server;
    }

    /**
    * Create Request
    * Create the xml request.
    *
    * @param string $method         Method on the XMLRPC Server to call.
    * @param array  $parameters     Array of parameters to be passed to the method (Optional).
    *
    * @return string xml request.
    */
    function createRequest($method, $parameters = array()) {
        $xml = "<?xml version=\"1.0\"?>\n";
        $xml .= "<methodCall>\n";
        $xml .= "\t<method>" . $method . "</method>\n";

        if (!empty($parameters)) {
            $xml .= "\t\t<params>\n";
            foreach ($parameters as $param) {
                $xml .= "\t\t\t<param>" . $this->xml_value($param) . "</param>\n";
            }
            $xml .= "\t\t</params>\n";
        }

        $xml .= "</methodCall>\n";
        return $xml;
    }

    /**
    * Decode
    * Decode the response from the server.
    *
    * @param string $xml    Response xml from the server.
    *
    * @return decoded values from server or false on error.
    */
    function decode($xml) {
        $data = simplexml_load_string($xml, null, LIBXML_NOCDATA);;

        if (!isset($data->params->param)) {
            return false;
        }

        $return_vals = array();

        foreach ($data->params->param as $param) {
            $return_vals[] = $this->xml_value($param, true);
        }

        return (count($return_val) == 1) ? $return_vals[0] : $return_vals;
    }

    /**
    * XML Value
    * Encode or decode values for xmlrpc use.
    *
    * @param mixed  $param   Array with type and value for encoding or SimpleXMLElement for decoding.
    * @param bool   $decode  Decode xml string, Default is false.
    *
    * @return encoded or decoded value.
    */
    function xml_value($param, $decode = false) {
        if ($decode) {
            return xml_decode_value($param);
        }
        return xml_encode_value($param);
    }

    /**
    * Fault XML
    * Get the fault info.
    *
    * @param SimpleXMLElement $xml
    *
    * @return value of SimpleXMLElement or false if no value.
    */
    function faultxml($xml) {
        if (!isset($xml->value)) {
            return false;
        }
        return $this->xml_value($xml->value, true);
    }

    /**
    * Execute
    * Execute a call to the XMLRPC Server.
    *
    * @param string $method         Name of method your calling.
    * @param array  $parameters     Parameters to pass to method (Optional).
    *
    * @return decoded response.
    */
    function execute($method, $parameters = null) {
        if (!function_exists('curl_init') && !function_exists('file_get_contents')) {
            throw new CribzXmlrpcException('You must have either Curl enabled or be able to use file_get_contents.');
        }

        $xml = $this->createRequest($method, $parameters);

        if (function_exists('curl_init')) {
            $response = $this->execute_curl($xml);
        } else {
            $response = $this->execute_file($xml);
        }

        $xml = simplexml_load_string($response, null, LIBXML_NOCDATA);

        if($xml === false) {
            throw new CribzXmlrpcException('Invalid response.');
        }

        if($xml->getName() == 'fault') {
            if ($response === false || !isset($response['faultString'])) {
                throw new CribzXmlrpcException('Unknown fault.');
            } else {
                $code = (isset($response['faultCode'])) ? (int) $response['faultCode'] : null;
                $message = $response['faultString'];
                throw new CribzXmlrpcException($message, $code);
            }
        }

        return $this->decode($xml);
    }

    /**
    * Execute Curl
    * Execute XMLRPC request using curl.
    *
    * @param string $xml    XML to be sent to XLRPC Server.
    *
    * @return string encoded response.
    */
    private function execute_curl($xml) {
        $options = array(
            CURLOPT_URL => $this->server,
            CURLOPT_PORT => 80,
            CURLOPT_USERAGENT => 'CribzXmlrpcClient',
            CURLOPT_TIMEOUT => 15,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_SSL_VERIFYPEER => false
        );

        $headers = array();
        $headers[] = 'Content-type: text/xml';
        $headers[] = 'Content-length: ' . strlen($xml) . "\r\n";
        $headers[] = $xml;

        $options[CURLOPT_HTTPHEADER] = $headers;

        $curl = curl_init();
        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $headers = curl_getinfo($curl);

        $errorNumber = curl_errno($curl);
        $errorMessage = curl_error($curl);

        curl_close($curl);

        if ($errorNumber != 0) {
            throw new CribzXmlrpcException('Error: '.$errorMessage);
        }

        if ($headers['http_code'] != 200) {
            throw new CribzXmlrpcException('Invalid header, status code returned: '.$headers['http_code']);
        }

        return $response;
    }

    /**
    * Execute File
    * Execute XMLRPC requesting using file_get_contents function.
    *
    * @param string $xml    XML to send to the XMLRPC Server.
    *
    * @return string encoded response.
    */
    private function execute_file($xml) {
        $options = array(
           'http' => array(
                'method' => 'POST',
                'headers' => "Content-type: text/xml\r\n" .
                             "Content-length: " . strlen($xml) . "\r\n" .
                             $xml . "\r\n",
           )
        );

        $context = stream_context_create($options);
        $response = file_get_contents($this->server, false, $context);

        if ($response === false) {
            throw new CribzXmlrpcExecption('Unable to make request to xmlrpc server @ '.$this->server);
        }

        return $response;
    }

    /**
    * XML Encode Value
    * Encoded values in the correct xml format.
    *
    * @param array $param   Array containing the type and value.
    *
    * @return string the xml representation of the value.
    */
    private function xml_encode_value($param) {
        switch ($param['type']) {
           case 'array':
                $xml = "<array>\n";
                $xml .= "\t<data>\n";

                foreach ($param['value'] as $item) {
                    $xml .= "\t\t<value>" . $this->xml_encode_value($item) . "</value>\n";
                }

                $xml .= "\t</data>\n";
                $xml .= "</array>";
                return $xml;
            break;

            case 'base64':
                return "<base64>" . (string) $param['value'] . "</base64>";
            break;

            case 'boolean':
                return $param['value'] ? "<boolean>1</boolean>" : "<boolean>0</boolean>";
            break;

            case 'date/time':
                if(is_integer($param['value'])) {
                    $param['value'] = date('c', (int) $param['value']);
                }

                return "<dateTime.iso8601>" . (string) $param['value'] . "</dateTime.iso8601>";
            break;

            case 'double':
                return "<double>" . (float) $param['value'] . "</double>";
            break;

            case 'int':
                return "<int>" . (int) $param['value'] . "</int>";
            break;

            case 'i4':
                return "<i4>" . (int) $param['value'] . "</i4>";
            break;

            case 'string':
                return "<string>" . (string) $param['value'] . "</string>";
            break;

            case 'struct':
                $xml = "<struct>\n";

                foreach ($param['value'] as $item) {
                    $xml .= "\t<member>\n";
                    $xml .= "\t\t<name>" . (string) $item['name'] . "</name>\n";
                    $xml .= "\t\t<value>" . $this->xml_encode_value($item) . "</value>\n";
                    $xml .= "\t</member>\n";
                }

                $xml .= "</struct>";
                return $xml;
            break;

            case 'nil':
                return "<nil/>";
            break;

            default:
                throw new CribzXmlrpcException('Invalid type: '.$param['type']);
        }
    }

    /**
    * XML Decode Value
    * Decode the xml values into php values.
    *
    * @param string $xml    The xml string to be parsed.
    *
    * @return mixed the php value.
    */
    private function xml_decode_value($xml) {
        foreach ($xml->children() as $element) {
            $type = $element->getName();

            switch ($type) {
                case 'array':
                    $return = array();
                    foreach ($element->data as $data) {
                        $return[] = $this->decodeValue($data->value);
                    }
                    return $return;
                break;

                case 'base64':
                    return base64_decode($element);
                break;

                case 'boolean':
                    if ($element == '1') {
                        return true;
                    }
                    return false;
                break;

                case 'dateTime.iso8601':
                    $value = strtotime((string) $element);
                    if ($value == 0) {
                        $value = (string) $element;
                    }
                    return $value;
                break;

                case 'double':
                    return (float) $element;
                break;

                case 'int':
                    return (int) $element;
                break;

                case 'i4':
                    return (int) $element;
                break;

                case 'string':
                    return (string) $element;
                break;

                case 'struct':
                    $return = array();
                    foreach ($element->member as $member) {
                        $name = (string) $member->name;
                        $value = $this->decodeValue($member->value);
                        $return[$name] = $value;
                    }
                    return $return;
                break;

                case 'nil':
                    return null;
                break;

                default:
                    throw new CribzXmlrpcException('Invalid type: '.$type);
            }
        }
    }
}


class CribzXmlrpcException extends CribzException {}
?>
