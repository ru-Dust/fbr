<?php

namespace WildWolf\FBR\Response;

class QueryFacesStatus extends Base
{
    private $list = [];

    public function __construct(\stdClass $data)
    {
        parent::__construct($data);

        if ($this->resultCode() == 3 && !empty($this->fotos[0]->foto)) {
            $this->decodeList($this->fotos[0]->foto);
        }
    }

    private function decodeList($encoded)
    {
        $decoded = array_map('trim', explode("\n", trim(base64_decode($encoded))));
        $list    = [];
        foreach ($decoded as $x) {
            list($segment, $bank, $id, $intname, $name) = explode('*', $x);
            $list[$segment . '*' . $bank . '*' . $id] = [$intname, substr($name, 1, -2)];
        }

        $this->list = $list;
    }

    public function pending() : bool
    {
        return $this->resultCode() == 2;
    }

    public function cacheable() : bool
    {
        return !$this->pending();
    }

    public function list() : array
    {
        return $this->list;
    }
}
