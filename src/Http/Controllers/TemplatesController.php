<?php

namespace Sendportal\Base\Http\Controllers;

use Sendportal\Base\Http\Requests\TemplateStoreRequest;
use Sendportal\Base\Http\Requests\TemplateUpdateRequest;
use Sendportal\Base\Repositories\TemplateTenantRepository;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TemplatesController extends Controller
{
    /** @var TemplateTenantRepository */
    protected $templates;

    public function __construct(TemplateTenantRepository $templates)
    {
        $this->templates = $templates;
    }

    /**
     * Show a listing of the resource.
     *
     * @return View
     * @throws Exception
     */
    public function index(): View
    {
        $templates = $this->templates->paginate(currentTeamId(), 'name');

        return view('templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('templates.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TemplateStoreRequest $request
     *
     * @return RedirectResponse
     * @throws Exception
     */
    public function store(TemplateStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['content'] = normalize_tags($data['content'], 'content');

        $this->templates->store(currentTeamId(), $data);

        return redirect()
            ->route('templates.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return View
     * @throws Exception
     */
    public function edit(int $id): View
    {
        $template = $this->templates->find(currentTeamId(), $id);

        return view('templates.edit', compact('template'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TemplateUpdateRequest $request
     * @param int $id
     *
     * @return RedirectResponse
     * @throws Exception
     */
    public function update(TemplateUpdateRequest $request, int $id): RedirectResponse
    {
        $data = $request->validated();

        $data['content'] = normalize_tags($data['content'], 'content');

        $this->templates->update(currentTeamId(), $id, $data);

        return redirect()
            ->route('templates.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy(int $id): RedirectResponse
    {
        $template = $this->templates->find(currentTeamId(), $id);

        // TODO(david): I don't think `is_in_use` has been implemented.
        if ($template->is_in_use) {
            return redirect()
                ->back()
                ->withErrors(['template' => __('Cannot delete a template that has been used.')]);
        }

        $this->templates->destroy(currentTeamId(), $template->id);

        return redirect()
            ->route('templates.index')
            ->with('success', __('Template successfully deleted.'));
    }
}