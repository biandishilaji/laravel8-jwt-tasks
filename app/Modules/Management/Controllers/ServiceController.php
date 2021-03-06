<?php
namespace App\Modules\Management\Controllers;

use App\Core\Http\Controllers\ApiController;
use App\Infrastructure\Repositories\Modules\Management\ServiceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class ServiceController extends ApiController
{

    private $_serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->_serviceRepository = $serviceRepository;
    }

    public function postCreateService(Request $request)
    {
        try {
            //comment
            $data = $request->only(['name', 'price', 'description']);

            $validator = Validator::make($data, [
                'name' => 'required',
                'price' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->responseError('Favor preencher todos os campos obrigatórios!', $validator->errors());
            }
            $service = $this->_serviceRepository->create($data);
            return $this->responseSuccess("Serviço cadastrado com sucesso!", []);

        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), []);
        }
    }
    public function getServiceById($id)
    {
        try {
            if(!$service = $this->_serviceRepository->find(($id))){
                throw new \Exception('Serviço não encontrado!');
            }
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), []);
        }
        return $this->responseSuccess('', $service);
    } 

    public function getServices()
    {
        try {
            return $this->responseSuccess('', $this->_serviceRepository->paginate(8));
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), []);
        }
    }
 
    public function deleteService($id)
    {
        try {
            $data = ['id' => $id];
            $validator = Validator::make($data, [
                'id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return $this->responseError('Informar um id válido!', $validator->errors());
            }
            $rows = $this->_serviceRepository->delete((int) $id);
            if ($rows == 0) {
                return $this->responseError('Serviço não encontrado!', []);
            } else {
                return $this->responseSuccess('Deletado com sucesso!', []);
            }
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), []);
        }
    }
    public function updateService(Request $request)
    {
        try {
            $data = $request->only(['id','name', 'price', 'description']);
            $validator = Validator::make($data, [
                'id' => 'required',
                'name' => 'required',
                'price' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->responseError('Favor preencher todos os campos obrigatórios!', $validator->errors());
            }
            $rows = $this->_serviceRepository->update($data, (int)$data['id']);
            if ($rows == 0) {
                return $this->responseError('Serviço não encontrado!', []);
            } else {
                return $this->responseSuccess('Editado com sucesso!', []);
            }
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), []);
        }
    }
}
