<?php

final class json extends \Controller_Class
{

    public function main()
    {
        $response = [];
        
        $response['title'] = "Lista de Compras";
        $response['content'] = $this->_model->read("tbl.data IS NULL", "nome");
		
        echo json_encode($response);
        die;
        
    }
    
    public function history()
    {
        $response = [];
        
        $response['title'] = "Histórico de Compras";
        $response['content'] = $this->_model->read("tbl.data != NULL", "nome");
        
        echo json_encode($response);
        die;
    }
    
    public function products()
    {
        $response = [];
        
        $response['title'] = "Lista de Produtos";
        $this->_model = new Model_Pdt();
        $response['content'] = $this->_model->read(null, "nome");
        
        echo json_encode($response);
        die;
    }

    protected function setModel()
    {
        $this->_model = new Model_Hst(true);
    }

    protected function setForm()
    {}
}

