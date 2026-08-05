<?php

namespace App\Controllers\Books;

use App\Models\AuthorModel;
use App\Models\BookModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\RESTful\ResourceController;

class AuthorsController extends ResourceController
{
    protected AuthorModel $authorModel;
    protected BookModel $bookModel;

    public function __construct()
    {
        $this->authorModel = new AuthorModel();
        $this->bookModel = new BookModel();
    }

    public function index()
    {
        $itemPerPage = 20;
        $search = $this->request->getGet('search');

        $query = $this->authorModel;
        if (!empty($search)) {
            $query = $query->like('name', $search, 'both', null, true);
        }

        $authors = $query->paginate($itemPerPage, 'authors');
        $bookCountInAuthors = [];

        foreach ($authors as $author) {
            $count = $this->bookModel
                ->where('author_id', $author['id'])
                ->orWhere('author', $author['name'])
                ->countAllResults();
            $bookCountInAuthors[] = $count;
        }

        $data = [
            'authors'            => $authors,
            'bookCountInAuthors' => $bookCountInAuthors,
            'pager'              => $this->authorModel->pager,
            'currentPage'        => $this->request->getVar('page_authors') ?? 1,
            'itemPerPage'        => $itemPerPage,
            'search'             => $search,
        ];

        return view('authors/index', $data);
    }

    public function new()
    {
        return view('authors/create', ['validation' => \Config\Services::validation()]);
    }

    public function create()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[127]',
        ];

        $name = trim($this->request->getVar('name') ?? '');

        // Check if existing author with normalized name exists
        $normalizedSearch = strtolower(preg_replace('/[\s\-]+/', ' ', $name));
        $allAuthors = $this->authorModel->findAll();
        $existing = null;
        foreach ($allAuthors as $a) {
            if (strtolower(preg_replace('/[\s\-]+/', ' ', $a['name'])) === $normalizedSearch) {
                $existing = $a;
                break;
            }
        }

        if ($this->request->isAJAX()) {
            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Nama pengarang minimal 2 karakter.'
                ]);
            }
            if ($existing) {
                return $this->response->setJSON([
                    'status' => true,
                    'data'   => ['id' => $existing['id'], 'name' => $existing['name']]
                ]);
            }

            $this->authorModel->insert(['name' => $name]);
            $newId = $this->authorModel->insertID();

            return $this->response->setJSON([
                'status' => true,
                'data'   => ['id' => $newId, 'name' => $name]
            ]);
        }

        if (!$this->validate($rules)) {
            session()->setFlashdata(['msg' => 'Gagal menambah pengarang. Silakan periksa kembali form.', 'error' => true]);
            return redirect()->back()->withInput();
        }

        if ($existing) {
            session()->setFlashdata(['msg' => 'Pengarang dengan nama tersebut sudah ada di database.']);
            return redirect()->to('admin/authors');
        }

        $this->authorModel->insert([
            'name' => $name,
        ]);

        session()->setFlashdata(['msg' => 'Pengarang berhasil ditambahkan.']);
        return redirect()->to('admin/authors');
    }

    public function edit($id = null)
    {
        $author = $this->authorModel->find($id);
        if (empty($author)) {
            throw new PageNotFoundException('Pengarang tidak ditemukan.');
        }

        return view('authors/edit', [
            'author'     => $author,
            'validation' => \Config\Services::validation()
        ]);
    }

    public function update($id = null)
    {
        $author = $this->authorModel->find($id);
        if (empty($author)) {
            throw new PageNotFoundException('Pengarang tidak ditemukan.');
        }

        $rules = [
            'name' => "required|min_length[2]|max_length[127]|is_unique[authors.name,id,{$id}]",
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata(['msg' => 'Gagal mengubah pengarang. Silakan periksa kembali form.', 'error' => true]);
            return redirect()->back()->withInput();
        }

        $this->authorModel->update($id, [
            'name' => trim($this->request->getVar('name')),
        ]);

        session()->setFlashdata(['msg' => 'Pengarang berhasil diperbarui.']);
        return redirect()->to('admin/authors');
    }

    public function delete($id = null)
    {
        $author = $this->authorModel->find($id);
        if (empty($author)) {
            throw new PageNotFoundException('Pengarang tidak ditemukan.');
        }

        $this->authorModel->delete($id);
        session()->setFlashdata(['msg' => 'Pengarang berhasil dihapus.']);
        return redirect()->to('admin/authors');
    }
}
