<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Data\Repositories\Users as UsersRepository;
use App\Http\Requests\UserStore as UserStoreRequest;
use App\Http\Requests\UserUpdate as UserUpdateRequest;
use App\Services\Authentication\Service as AuthenticationService;
use Illuminate\Http\Request;

use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class Users extends Controller
{
    /**
     * @var UsersRepository
     */
    private $usersRepository;

    /**
     * Users constructor.
     *
     * @param UsersRepository $usersRepository
     */

    public function export(Request $request)
    {
        set_time_limit(0);
        return Excel::download(new UsersExport($request->data_ini, $request->data_fim), 'users.csv');
    }




    public function __construct(UsersRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return $this->view('admin.users.form')
            ->with('assignableRoles', \Auth::user()->assignable_roles)
            ->with('user', $this->usersRepository->new())
            ->with('mode', 'create');
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function show($id)
    {
        $user = app(UsersRepository::class)->findById($id);

        return $this->view('admin.users.form')
            ->with('assignableRoles', \Auth::user()->assignable_roles)
            ->with('mode', 'show')
            ->with('user', $user->append('roles'));
    }

    public function normalizeUserInfoFromRequest(UserStoreRequest $request)
    {
        //Get the user info from remote
        preg_match('/(.*?)@(.*)/', $request->get('email'), $output_array);

        if (isset($output_array[1])) {
            $userResponse = app(
                AuthenticationService::class
            )->userInfoByUsername($output_array[1]);

            $request->merge([
                'email' => strtolower($userResponse['email'][0]),
                'name' => $userResponse['name'][0],
                'username' => strtolower($output_array[1]),
            ]);

            return $request;
        }
    }

    /**
     * @param UserStoreRequest     $request
     * @param UsersRepository $repository
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(
        UserStoreRequest $request,
        UsersRepository $repository
    ) {
        $user = app(UsersRepository::class)->storeFromArray($request->all());

        $user->syncRoles($request->get('roles_array'));

        return redirect()->route('users.index');
    }

    /**
     * @return $this
     */
    public function index()
    {
        return $this->view('admin.users.index')->with(
            'users',
            $this->usersRepository
                ->disablePagination()
                ->all()
                ->appends('roles_string')
        );
    }

    /**
     * @param UserUpdateRequest $request
     * @param $id
     * @return mixed
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $user = app(UsersRepository::class)->update($id, $request->all());

        $user->syncRoles($request->get('roles_array'));

        return redirect()->route('users.index');
    }
}
