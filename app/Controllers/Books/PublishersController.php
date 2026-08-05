<?php

namespace App\Controllers\Books;

use App\Models\BookModel;
use App\Models\PublisherModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\RESTful\ResourceController;

class PublishersController extends ResourceController
{
    protected PublisherModel $publisherModel;
    protected BookModel $bookModel;

    public function __construct()
    {
        $this->publisherModel = new PublisherModel();
        $this->bookModel = new BookModel();
    }

    public function index()
    {
        $itemPerPage = 20;
        $search = $this->request->getGet('search');

        $query = $this->publisherModel;
        if (!empty($search)) {
            $query = $query->like('name', $search, 'both', null, true);
        }

        $publishers = $query->paginate($itemPerPage, 'publishers');
        $bookCountInPublishers = [];

        foreach ($publishers as $publisher) {
            $count = $this->bookModel
                ->where('publisher_id', $publisher['id'])
                ->orWhere('publisher', $publisher['name'])
                ->countAllResults();
            $bookCountInPublishers[] = $count;
        }

        $data = [
            'publishers'            => $publishers,
            'bookCountInPublishers' => $bookCountInPublishers,
            'pager'                 => $this->publisherModel->pager,
            'currentPage'           => $this->request->getVar('page_publishers') ?? 1,
            'itemPerPage'           => $itemPerPage,
            'search'                => $search,
        ];

        return view('publishers/index', $data);
    }

    public function new()
    {
        return view('publishers/create', ['validation' => \Config\Services::validation()]);
    }

    public function create()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[127]',
        ];

        $name = trim($this->request->getVar('name') ?? '');

        // Check if existing publisher with normalized name exists
        $normalizedSearch = strtolower(preg_replace('/[\s\-]+/', ' ', $name));
        $allPublishers = $this->publisherModel->findAll();
        $existing = null;
        foreach ($allPublishers as $p) {
            if (strtolower(preg_replace('/[\s\-]+/', ' ', $p['name'])) === $normalizedSearch) {
                $existing = $p;
                break;
            }
        }

        if ($this->request->isAJAX()) {
            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Nama penerbit minimal 2 karakter.'
                ]);
            }
            if ($existing) {
                return $this->response->setJSON([
                    'status' => true,
                    'data'   => ['id' => $existing['id'], 'name' => $existing['name']]
                ]);
            }

            $this->publisherModel->insert(['name' => $name]);
            $newId = $this->publisherModel->insertID();

            return $this->response->setJSON([
                'status' => true,
                'data'   => ['id' => $newId, 'name' => $name]
            ]);
        }

        if (!$this->validate($rules)) {
            session()->setFlashdata(['msg' => 'Gagal menambah penerbit. Silakan periksa kembali form.', 'error' => true]);
            return redirect()->back()->withInput();
        }

        if ($existing) {
            session()->setFlashdata(['msg' => 'Penerbit dengan nama tersebut sudah ada di database.']);
            return redirect()->to('admin/publishers');
        }

        $this->publisherModel->insert([
            'name' => $name,
        ]);

        session()->setFlashdata(['msg' => 'Penerbit berhasil ditambahkan.']);
        return redirect()->to('admin/publishers');
    }

    public function edit($id = null)
    {
        $publisher = $this->publisherModel->find($id);
        if (empty($publisher)) {
            throw new PageNotFoundException('Penerbit tidak ditemukan.');
        }

        return view('publishers/edit', [
            'publisher'  => $publisher,
            'validation' => \Config\Services::validation()
        ]);
    }

    public function update($id = null)
    {
        $publisher = $this->publisherModel->find($id);
        if (empty($publisher)) {
            throw new PageNotFoundException('Penerbit tidak ditemukan.');
        }

        $rules = [
            'name' => "required|min_length[2]|max_length[127]|is_unique[publishers.name,id,{$id}]",
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata(['msg' => 'Gagal mengubah penerbit. Silakan periksa kembali form.', 'error' => true]);
            return redirect()->back()->withInput();
        }

        $this->publisherModel->update($id, [
            'name' => trim($this->request->getVar('name')),
        ]);

        session()->setFlashdata(['msg' => 'Penerbit berhasil diperbarui.']);
        return redirect()->to('admin/publishers');
    }

    public function delete($id = null)
    {
        $publisher = $this->publisherModel->find($id);
        if (empty($publisher)) {
            throw new PageNotFoundException('Penerbit tidak ditemukan.');
        }

        $this->publisherModel->delete($id);
        session()->setFlashdata(['msg' => 'Penerbit berhasil dihapus.']);
        return redirect()->to('admin/publishers');
    }
}
