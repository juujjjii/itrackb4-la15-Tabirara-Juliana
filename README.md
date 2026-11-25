## 1. Authentication and Authorization
    Last week, my project answered the question, **“Who are you?”** through registration, login, logout, and authentication. This week, it answered **“What are you allowed to do?”** through `SubjectPolicy`, which controls who can create, update, and delete subjects. The policy answers the second question, while `routes/web.php` defines the routes and authentication middleware for the first.

## 2. Why `role` Is Not in `$fillable`
    The `role` column is not included in the `$fillable` array in `app/Models/User.php` because I do not want users to assign their own roles through mass assignment. I was still able to set `$user->role = 'admin'` and call `$user->save()` in Tinker because direct property assignment and saving are not blocked by `$fillable`. The `$fillable` property protects against unwanted mass assignment of fields, such as a user submitting `role=admin` through a registration form.

## 3. 403 Forbidden vs. Login Page
    When a signed-in regular user visits `/subjects/create`, `Gate::authorize('create', Subject::class)` in `SubjectsController` checks the `SubjectPolicy` and returns a 403 Forbidden response because the user is not an admin. When a guest visits the same address, the `auth` middleware on the route redirects the guest to the login page because authentication is required first. These responses are deliberately different because a guest needs to sign in, while an authenticated non-admin is already identified but does not have permission.

## 4. Public List and Detail Pages
    My `subjects.index` and `subjects.show` routes remain accessible to everyone because the `viewAny` and `view` methods in `SubjectPolicy` return `true`. I made this decision so guests and regular users can still browse subject records, including their codes, titles, units, and prerequisites, without having permission to modify them.

## 5. Why Use a Policy?
    My `SubjectsController` uses `Gate::authorize()` to enforce permissions, while `resources/views/subjects/index.blade.php` uses `@can` and `@cannot` to show or hide the Add, Edit, and Delete controls. If I wrote the role checks separately in both places, their rules could become inconsistent when I update one but forget the other. Using `SubjectPolicy` gives the controller and the view one shared source of authorization rules.

## 6. Session Lifetime
    My `.env` file uses `SESSION_LIFETIME=120`, which counts the minutes of session inactivity before the session expires; it does not count the total time a user spends logged in. There was no warning because Laravel expires the session on the server, and I did not implement a browser countdown or advance notification. When the expired user visits a protected route, the authentication middleware redirects them to the login page.