<?php

namespace App\Controllers\Books;

use App\Controllers\BaseController;
use App\Models\AuthorModel;
use App\Models\PublisherModel;
use App\Models\CategoryModel;
use App\Models\RackModel;

class BookAttributesController extends BaseController
{
    protected $authorModel;
    protected $publisherModel;
    protected $categoryModel;
    protected $rackModel;

    public function __construct()
    {
        $this->authorModel    = new AuthorModel();
        $this->publisherModel = new PublisherModel();
        $this->categoryModel  = new CategoryModel();
        $this->rackModel      = new RackModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $activeTab = $this->request->getGet('tab') ?? 'authors';

        $authors    = $this->authorModel->orderBy('name', 'ASC')->findAll();
        $publishers = $this->publisherModel->orderBy('name', 'ASC')->findAll();
        $categories = $this->categoryModel->orderBy('name', 'ASC')->findAll();
        $racks      = $this->rackModel->orderBy('name', 'ASC')->findAll();

        return view('books/attributes/index', [
            'title'      => 'Master Atribut Buku',
            'activeTab'  => $activeTab,
            'authors'    => $authors,
            'publishers' => $publishers,
            'categories' => $categories,
            'racks'      => $racks,
        ]);
    }

    // === PENGARANG / AUTHOR HANDLERS ===
    public function storeAuthor()
    {
        $name = trim((string)$this->request->getPost('name'));
        if (!empty($name)) {
            $this->authorModel->insert(['name' => $name]);
            return redirect()->to(base_url('admin/book-attributes?tab=authors'))->with('success', 'Pengarang berhasil ditambahkan!');
        }
        return redirect()->to(base_url('admin/book-attributes?tab=authors'))->with('error', 'Nama pengarang tidak boleh kosong.');
    }

    public function updateAuthor($id)
    {
        $name = trim((string)$this->request->getPost('name'));
        if (!empty($name)) {
            $this->authorModel->update($id, ['name' => $name]);
            return redirect()->to(base_url('admin/book-attributes?tab=authors'))->with('success', 'Data pengarang berhasil diperbarui!');
        }
        return redirect()->to(base_url('admin/book-attributes?tab=authors'))->with('error', 'Nama pengarang tidak boleh kosong.');
    }

    public function deleteAuthor($id)
    {
        $this->authorModel->delete($id);
        return redirect()->to(base_url('admin/book-attributes?tab=authors'))->with('success', 'Pengarang berhasil dihapus.');
    }

    // === PENERBIT / PUBLISHER HANDLERS ===
    public function storePublisher()
    {
        $name = trim((string)$this->request->getPost('name'));
        if (!empty($name)) {
            $this->publisherModel->insert(['name' => $name]);
            return redirect()->to(base_url('admin/book-attributes?tab=publishers'))->with('success', 'Penerbit berhasil ditambahkan!');
        }
        return redirect()->to(base_url('admin/book-attributes?tab=publishers'))->with('error', 'Nama penerbit tidak boleh kosong.');
    }

    public function updatePublisher($id)
    {
        $name = trim((string)$this->request->getPost('name'));
        if (!empty($name)) {
            $this->publisherModel->update($id, ['name' => $name]);
            return redirect()->to(base_url('admin/book-attributes?tab=publishers'))->with('success', 'Data penerbit berhasil diperbarui!');
        }
        return redirect()->to(base_url('admin/book-attributes?tab=publishers'))->with('error', 'Nama penerbit tidak boleh kosong.');
    }

    public function deletePublisher($id)
    {
        $this->publisherModel->delete($id);
        return redirect()->to(base_url('admin/book-attributes?tab=publishers'))->with('success', 'Penerbit berhasil dihapus.');
    }

    // === KATEGORI / CATEGORY HANDLERS ===
    public function storeCategory()
    {
        $name = trim((string)$this->request->getPost('name'));
        if (!empty($name)) {
            $this->categoryModel->insert(['name' => $name]);
            return redirect()->to(base_url('admin/book-attributes?tab=categories'))->with('success', 'Kategori berhasil ditambahkan!');
        }
        return redirect()->to(base_url('admin/book-attributes?tab=categories'))->with('error', 'Nama kategori tidak boleh kosong.');
    }

    public function updateCategory($id)
    {
        $name = trim((string)$this->request->getPost('name'));
        if (!empty($name)) {
            $this->categoryModel->update($id, ['name' => $name]);
            return redirect()->to(base_url('admin/book-attributes?tab=categories'))->with('success', 'Data kategori berhasil diperbarui!');
        }
        return redirect()->to(base_url('admin/book-attributes?tab=categories'))->with('error', 'Nama kategori tidak boleh kosong.');
    }

    public function deleteCategory($id)
    {
        $this->categoryModel->delete($id);
        return redirect()->to(base_url('admin/book-attributes?tab=categories'))->with('success', 'Kategori berhasil dihapus.');
    }

    // === RAK BUKU / RACK HANDLERS ===
    public function storeRack()
    {
        $name  = trim((string)$this->request->getPost('name'));
        $floor = trim((string)$this->request->getPost('floor'));
        if (!empty($name)) {
            $this->rackModel->insert(['name' => $name, 'floor' => $floor]);
            return redirect()->to(base_url('admin/book-attributes?tab=racks'))->with('success', 'Rak buku berhasil ditambahkan!');
        }
        return redirect()->to(base_url('admin/book-attributes?tab=racks'))->with('error', 'Nama rak tidak boleh kosong.');
    }

    public function updateRack($id)
    {
        $name  = trim((string)$this->request->getPost('name'));
        $floor = trim((string)$this->request->getPost('floor'));
        if (!empty($name)) {
            $this->rackModel->update($id, ['name' => $name, 'floor' => $floor]);
            return redirect()->to(base_url('admin/book-attributes?tab=racks'))->with('success', 'Data rak berhasil diperbarui!');
        }
        return redirect()->to(base_url('admin/book-attributes?tab=racks'))->with('error', 'Nama rak tidak boleh kosong.');
    }

    public function deleteRack($id)
    {
        $this->rackModel->delete($id);
        return redirect()->to(base_url('admin/book-attributes?tab=racks'))->with('success', 'Rak buku berhasil dihapus.');
    }
}
