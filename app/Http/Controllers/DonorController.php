<?php

namespace App\Http\Controllers;

use App\DataTables\DonorDataTable;
use App\Http\Requests\BulkRequest;
use App\Http\Requests\DonorRequest;
use App\Models\Donor;
use App\Models\ParserTask;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonorController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export()
    {
        $tmp_path = storage_path() . '/temp_export';
        file_put_contents(storage_path() . '/temp_export', Donor::all()->toJson());
        return response()->download($tmp_path, 'доноры.json')->deleteFileAfterSend();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function import()
    {

        $file = request()->file('file');
        $donors = json_decode($file->get());

        try {
            \DB::beginTransaction();

            Donor::all()->each->delete();
            foreach ($donors as $donor_arr) {
                $donor = new Donor((array) $donor_arr);
                $donor->id = $donor_arr->id;
                $donor->save();
            }
            \DB::commit();
            \Toastr::success('Ок','Импорт завершен');
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Toastr::error($e->getMessage(),'Ошибка при импорте');
        }

        return redirect()->back();
    }

    /**
     * Display a listing of the resource.
     *
     * @param DonorDataTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function index(DonorDataTable $dataTable)
    {
        if (request()->ajax()) {
            return $dataTable->ajax();
        }

        return view('admin.donors.index', [
            'html' => $dataTable->html(),
        ]);
    }

    public function bulk(BulkRequest $request)
    {
        if ($request->action == 'parse') {
            $donors      = Donor::with(['parsed_companies' => function (HasMany $query) {
                $query->whereHas('company');
            }])->findMany($request->ids);
            $donor_pages = $donors->flatMap->parsed_companies->map->donor_page;
            ParserTask::dispatch($donor_pages, 'companies');
        }
        return redirect()->back();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.donors.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DonorRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(DonorRequest $request)
    {
        $donor = Donor::create($request->all());
        return redirect()->route('donors.edit', [
            'donor' => $donor,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Donor $donor
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Donor $donor)
    {
        return view('admin.donors.form', [
            'donor' => $donor,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Donor $donor
     * @return \Illuminate\Http\Response
     */
    public function edit(Donor $donor)
    {
        return view('admin.donors.form', [
            'donor' => $donor,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param DonorRequest $request
     * @param Donor $donor
     * @return \Illuminate\Http\Response
     */
    public function update(DonorRequest $request, Donor $donor)
    {

        $donor->update($request->all());
        return redirect()->back()->with('success', 'Компания изменена!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Donor $donor
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Donor $donor)
    {
        $donor->delete();
        return redirect()->back();
    }
}
