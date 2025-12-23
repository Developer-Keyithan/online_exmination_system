// permissionModelController.js
app.factory("permissionModalController", [
    "API_URL",
    "window",
    "jQuery",
    "$http",
    "$sce",
    "$rootScope",
    "$compile",
    "$timeout",
    function (
        API_URL,
        window,
        $,
        $http,
        $sce,
        $rootScope,
        $compile,
        $timeout
    ) {
        return function ($scope) {

            $rootScope.staticPermissionData = {
                "dashboard": {
                    "name": "Dashboard",
                    "access": { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] },
                    "permissions": {
                        "dashboard.view": { label: "View Dashboard", access: { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] } },
                        // "dashboard.customize": { label: "Customize Dashboard", access: { role: [1, 2, 3], permissions: [] } },
                        "dashboard.reports": { label: "View Dashboard Reports", access: { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] } },
                        // "dashboard.widgets": { label: "Manage Dashboard Widgets", access: { role: [1, 2, 3], permissions: [] } },
                        "dashboard.analytics": { label: "View Analytics", access: { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] } },
                        "dashboard.notifications": { label: "View Notifications", access: { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] } },
                        // "dashboard.activity": { label: "View Activity Log", access: { role: [1, 2, 3], permissions: [] } },
                        // "dashboard.export": { label: "Export Dashboard Data", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                        // "dashboard.settings": { label: "Manage Dashboard Settings", access: { role: [1, 2, 3], permissions: [] } },
                        // "dashboard.users": { label: "View User Statistics", access: { role: [1, 2, 3], permissions: [] } },
                        // "dashboard.roles": { label: "View Role Statistics", access: { role: [1, 2, 3], permissions: [] } },
                        // "dashboard.permissions": { label: "View Permission Statistics", access: { role: [1, 2, 3], permissions: [] } },
                        // "dashboard.themes": { label: "Change Dashboard Theme", access: { role: [1, 2, 3], permissions: [] } },
                        // "dashboard.layout": { label: "Manage Dashboard Layout", access: { role: [1, 2, 3], permissions: [] } },
                        // "dashboard.data": { label: "Access Dashboard Data", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                        // "dashboard.integration": { label: "Manage Integrations", access: { role: [1, 2, 3], permissions: [] } },
                        // "dashboard.support": { label: "Access Support Features", access: { role: [1], permissions: [] } },
                        // "dashboard.updates": { label: "Check for Updates", access: { role: [1, 2, 3], permissions: [] } },
                        // "dashboard.backup": { label: "Create Backups", access: { role: [1, 2, 3], permissions: [] } },
                        // "dashboard.restore": { label: "Restore from Backup", access: { role: [1, 2, 3], permissions: [] } }
                    }
                },
                // "courses": {
                //     "name": "Courses",
                //     "access": { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] },
                //     "permissions": {
                //         "courses.manage": { label: "Manage Courses", access: { role: [1, 2, 3, 4], permissions: [] } },
                //         "courses.view": { label: "View Courses", access: { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] } },
                //         "courses.create": { label: "Create Courses", access: { role: [1, 2, 3, 4], permissions: [] } },
                //         "courses.my_courses": { label: "View My Courses", access: { role: [1, 5, 6, 7], permissions: [] } },
                //         "courses.edit": { label: "Edit Courses", access: { role: [1, 2, 3, 4], permissions: [] } },
                //         "courses.delete": { label: "Delete Courses", access: { role: [1, 2, 3, 4], permissions: [] } },
                //         "courses.assign": { label: "Assign Courses", access: { role: [1, 2, 3, 4], permissions: [] } },
                //         "courses.unassign": { label: "Unassign Courses", access: { role: [1, 2, 3, 4], permissions: [] } }
                //     }
                // },
                // "lectures": {
                //     "name": "Lectures",
                //     "access": { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] },
                //     "permissions": {
                //         "lectures.view": { label: "View Lectures", access: { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] } },
                //         "lectures.create": { label: "Create Lectures", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "lectures.my": { label: "View My Lectures", access: { role: [1, 4, 5, 6, 7], permissions: [] } },
                //         "lectures.edit": { label: "Edit Lectures", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "lectures.delete": { label: "Delete Lectures", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "lectures.schedule": { label: "Schedule Lectures", access: { role: [1, 2, 3, 4, 5], permissions: [] } }
                //     }
                // },
                "exams": {
                    "name": "Exams",
                    "access": { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] },
                    "permissions": {
                        "exams.create": { label: "Create Exams", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                        "exams.edit": { label: "Edit Exams", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                        "exams.view": { label: "View Exams", access: { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] } },
                        "exams.delete": { label: "Delete Exams", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                        "exams.attempt": { label: "Attempt Exams", access: { role: [1, 6], permissions: [] } },
                        "exams.all": { label: "View All Exams", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                        "exams.my": { label: "View My Exams", access: { role: [1, 5, 6, 7], permissions: [] } },
                        "exams.manage": { label: "Manage Exams", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                        "exams.schedule": { label: "Schedule Exams", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                        "exams.preview": { label: "Preview Exams", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                        "exams.register": { label: "Register for Exams", access: { role: [1, 6], permissions: [] } }
                    }
                },
                "questions": {
                    "name": "Questions",
                    "access": { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] },
                    "permissions": {
                        "questions.create": { label: "Create Questions", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                        "questions.view": { label: "View Questions", access: { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] } },
                        "questions.edit": { label: "Edit Questions", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                        "questions.edit.my": { label: "Edit My Questions", access: { role: [1, 4, 5], permissions: [] } },
                        "questions.delete": { label: "Delete Questions", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                        "questions.delete.my": { label: "Delete My Questions", access: { role: [1, 4, 5], permissions: [] } },
                        // "questions.bank": { label: "Access Question Bank", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                        "questions.my": { label: "View My Questions", access: { role: [1, 4, 5], permissions: [] } },
                        "questions.all": { label: "View All Questions", access: { role: [1, 2, 3], permissions: [] } },
                        // "questions.assign": { label: "Assign Questions", access: { role: [1, 2, 3, 4, 5], permissions: [] } }
                    }
                },
                // "past_papers": {
                //     "name": "Past Papers",
                //     "access": { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] },
                //     "permissions": {
                //         "past_papers.view": { label: "View Past Papers", access: { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] } },
                //         "past_papers.upload": { label: "Upload Past Papers", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "past_papers.edit": { label: "Edit Past Papers", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "past_papers.delete": { label: "Delete Past Papers", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "past_papers.all": { label: "View All Past Papers", access: { role: [1, 2, 3], permissions: [] } },
                //         "past_papers.my": { label: "View My Past Papers", access: { role: [1, 5, 6, 7], permissions: [] } }
                //     }
                // },
                "results": {
                    "name": "Results",
                    "access": { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] },
                    "permissions": {
                        "results.view": { label: "View Results", access: { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] } },
                        "results.publish": { label: "Publish Results", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                        "results.all": { label: "View All Results", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                        "results.my": { label: "View My Results", access: { role: [1, 6, 7], permissions: [] } },
                        "results.edit": { label: "Edit Results", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                        "results.delete": { label: "Delete Results", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                        "results.create": { label: "Create Results", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                        "results.manage": { label: "Manage Results", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                        "results.review": { label: "Review Results", access: { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] } }
                    }
                },
                // "attendance": {
                //     "name": "Attendance",
                //     "access": { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] },
                //     "permissions": {
                //         "attendance.manage": { label: "Manage Attendance", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "attendance.view": { label: "View Attendance", access: { role: [1, 2, 3, 4, 5, 6], permissions: [] } },
                //         "attendance.mark": { label: "Mark Attendance", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "attendance.my": { label: "View My Attendance", access: { role: [1, 6, 7], permissions: [] } },
                //         "attendance.all": { label: "View All Attendance", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "attendance.change": { label: "Change Attendance", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "attendance.delete": { label: "Delete Attendance", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "attendance.edit": { label: "Edit Attendance", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "attendance.create": { label: "Create Attendance Records", access: { role: [1, 2, 3, 4, 5], permissions: [] } }
                //     }
                // },
                // "notifications": {
                //     "name": "Notifications",
                //     "access": { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] },
                //     "permissions": {
                //         "notifications.view": { label: "View Notifications", access: { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] } },
                //         "notifications.send": { label: "Send Notifications", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "notifications.manage": { label: "Manage Notifications", access: { role: [1, 2, 3], permissions: [] } },
                //         "notifications.my": { label: "View My Notifications", access: { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] } },
                //         "notifications.all": { label: "View All Notifications", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "notifications.templates": { label: "Manage Notification Templates", access: { role: [1, 2, 3], permissions: [] } },
                //         "notifications.settings": { label: "Manage Notification Settings", access: { role: [1, 2, 3], permissions: [] } }
                //     }
                // },
                "users": {
                    "name": "User Management",
                    "access": { role: [1, 2, 3], permissions: [] },
                    "permissions": {
                        "users.manage": { label: "Manage Users", access: { role: [1, 2, 3], permissions: [] } },
                        "users.create": { label: "Create Users", access: { role: [1, 2, 3], permissions: [] } },
                        "users.edit": { label: "Edit Users", access: { role: [1, 2, 3], permissions: [] } },
                        "users.delete": { label: "Delete Users", access: { role: [1, 2, 3], permissions: [] } },
                        "users.view": { label: "View Users", access: { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] } },
                        "users.all": { label: "View All Users", access: { role: [1, 2, 3], permissions: [] } },
                        "users.add": { label: "Add Users", access: { role: [1, 2, 3], permissions: [] } },
                        "students.manage": { label: "Manage Students", access: { role: [1, 2, 3, 5], permissions: [] } },
                        // "lecturers.manage": { label: "Manage Lecturers", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                        // "parents.manage": { label: "Manage Parents", access: { role: [1, 2, 3, 5], permissions: [] } },
                        // "hod.manage": { label: "Manage HODs", access: { role: [1, 2, 3], permissions: [] } },
                        "groups.manage": { label: "Manage User Groups", access: { role: [1, 2, 3], permissions: [] } }
                    }
                },
                // "students": {
                //     "name": "Students",
                //     "access": { role: [1, 2, 3, 5, 6], permissions: [] },
                //     "permissions": {
                //         "students.create": { label: "Create Students", access: { role: [1, 2, 3, 5], permissions: [] } },
                //         "students.edit": { label: "Edit Students", access: { role: [1, 2, 3, 5], permissions: [] } },
                //         "students.delete": { label: "Delete Students", access: { role: [1, 2, 3, 5], permissions: [] } },
                //         "students.view": { label: "View Students", access: { role: [1, 2, 3, 5, 6], permissions: [] } },
                //         "students.link": { label: "Link Students", access: { role: [1, 2, 3, 5], permissions: [] } },
                //         "students.link.classes": { label: "Link Students to Classes", access: { role: [1, 2, 3, 5], permissions: [] } },
                //         "students.link.subjects": { label: "Link Students to Subjects", access: { role: [1, 2, 3, 5], permissions: [] } },
                //         "students.link.parents": { label: "Link Students to Parents", access: { role: [1, 2, 3, 5], permissions: [] } },
                //         "students.link.guardians": { label: "Link Students to Guardians", access: { role: [1, 2, 3, 5], permissions: [] } },
                //         "students.link.attendance": { label: "Link Students to Attendance", access: { role: [1, 2, 3, 5], permissions: [] } },
                //         "students.link.exams": { label: "Link Students to Exams", access: { role: [1, 2, 3, 5], permissions: [] } },
                //         "students.link.results": { label: "Link Students to Results", access: { role: [1, 2, 3, 5], permissions: [] } },
                //         "students.link.behaviour": { label: "Link Students to Behaviour Records", access: { role: [1, 2, 3, 5], permissions: [] } },
                //         "students.link.medical": { label: "Link Students to Medical Records", access: { role: [1, 2, 3, 5], permissions: [] } },
                //         "students.link.documents": { label: "Link Students to Documents", access: { role: [1, 2, 3, 5], permissions: [] } },
                //         "students.link.transport": { label: "Link Students to Transport", access: { role: [1, 2, 3, 5], permissions: [] } },
                //         "students.link.fee": { label: "Link Students to Fee Records", access: { role: [1, 2, 3, 5], permissions: [] } }
                //     }
                // },
                // "parents": {
                //     "name": "Parents",
                //     "access": { role: [1, 2, 3, 5, 7], permissions: [] },
                //     "permissions": {
                //         "parents.create": { label: "Create Parents", access: { role: [1, 2, 3, 5], permissions: [] } },
                //         "parents.edit": { label: "Edit Parents", access: { role: [1, 2, 3, 5], permissions: [] } },
                //         "parents.delete": { label: "Delete Parents", access: { role: [1, 2, 3, 5], permissions: [] } },
                //         "parents.view": { label: "View Parents", access: { role: [1, 2, 3, 5, 7], permissions: [] } },
                //         "parents.link": { label: "Link Parents", access: { role: [1, 2, 3, 5], permissions: [] } },
                //         "parents.link.students": { label: "Link Parents to Students", access: { role: [1, 2, 3, 5], permissions: [] } },
                //         "parents.link.documents": { label: "Link Parents to Documents", access: { role: [1, 2, 3, 5], permissions: [] } }
                //     }
                // },
                // "lecturers": {
                //     "name": "Lecturers",
                //     "access": { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] },
                //     "permissions": {
                //         "lecturers.create": { label: "Create Lecturers", access: { role: [1, 2, 3], permissions: [] } },
                //         "lecturers.edit": { label: "Edit Lecturers", access: { role: [1, 2, 3], permissions: [] } },
                //         "lecturers.delete": { label: "Delete Lecturers", access: { role: [1, 2, 3], permissions: [] } },
                //         "lecturers.view": { label: "View Lecturers", access: { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] } },
                //         "lecturers.view.my": { label: "View Under HOD Lecturers", access: { role: [1, 2, 3, 4], permissions: [] } },
                //         "lecturers.link.courses": { label: "Link Lecturers to Courses", access: { role: [1, 2, 3, 4], permissions: [] } },
                //         "lecturers.link.classes": { label: "Link Lecturers to Classes", access: { role: [1, 2, 3, 4], permissions: [] } },
                //         "lecturers.link.departments": { label: "Link Lecturers to Departments", access: { role: [1, 2, 3, 4], permissions: [] } },
                //         "lecturers.link.lectures": { label: "Link Lecturers to Lectures", access: { role: [1, 2, 3, 4, 5], permissions: [] } }
                //     }
                // },
                // "departments": {
                //     "name": "Departments",
                //     "access": { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] },
                //     "permissions": {
                //         "departments.manage": { label: "Manage Departments", access: { role: [1, 2, 3], permissions: [] } },
                //         "departments.view": { label: "View Departments", access: { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] } },
                //         "departments.create": { label: "Create Departments", access: { role: [1, 2, 3], permissions: [] } },
                //         "departments.edit": { label: "Edit Departments", access: { role: [1, 2, 3], permissions: [] } },
                //         "departments.delete": { label: "Delete Departments", access: { role: [1, 2, 3], permissions: [] } },
                //         "departments.link": { label: "Link Departments", access: { role: [1, 2, 3], permissions: [] } },
                //         "departments.link.courses": { label: "Link Departments to Courses", access: { role: [1, 2, 3], permissions: [] } },
                //         "departments.link.lecturers": { label: "Link Departments to Lecturers", access: { role: [1, 2, 3], permissions: [] } },
                //         "departments.link.students": { label: "Link Departments to Students", access: { role: [1, 2, 3], permissions: [] } },
                //         "departments.link.staff": { label: "Link Departments to Staff", access: { role: [1, 2, 3], permissions: [] } },
                //         "departments.link.reports": { label: "Link Departments to Reports", access: { role: [1, 2, 3], permissions: [] } },
                //         "departments.link.branch": { label: "Link Departments to Branches", access: { role: [1, 2, 3], permissions: [] } }
                //     }
                // },
                // "HOD": {
                //     "name": "HOD",
                //     "access": { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] },
                //     "permissions": {
                //         "hod.view": { label: "View All HODs", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "hod.create": { label: "Create HOD", access: { role: [1, 2, 3], permissions: [] } },
                //         "hod.edit": { label: "Edit HOD", access: { role: [1, 2, 3], permissions: [] } },
                //         "hod.delete": { label: "Delete HOD", access: { role: [1, 2, 3], permissions: [] } },
                //         "hod.link.departments": { label: "Link HOD to Departments", access: { role: [1, 2, 3], permissions: [] } },
                //         "hod.my": { label: "View My HOD", access: { role: [1, 6, 7], permissions: [] } }
                //     }
                // },
                // "reports": {
                //     "name": "Reports",
                //     "access": { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] },
                //     "permissions": {
                //         "reports.view": { label: "View Reports", access: { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] } },
                //         "reports.view.my": { label: "View My Reports", access: { role: [1, 4, 5, 6, 7], permissions: [] } },
                //         "reports.create": { label: "Create Reports", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "reports.edit": { label: "Edit Reports", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "reports.edit.my": { label: "Edit My Reports", access: { role: [1, 4, 5], permissions: [] } },
                //         "reports.delete.my": { label: "Delete My Reports", access: { role: [1, 4, 5], permissions: [] } },
                //         "reports.download": { label: "Download Reports", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "reports.download.my": { label: "Download My Reports", access: { role: [1, 4, 5, 6, 7], permissions: [] } },
                //         "reports.exam": { label: "Access Exam Reports", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "reports.exam.create": { label: "Create Exam Reports", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "reports.exam.edit": { label: "Edit Exam Reports", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "reports.exam.edit.my": { label: "Edit My Exam Reports", access: { role: [1, 4, 5], permissions: [] } },
                //         "reports.exam.delete": { label: "Delete Exam Reports", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "reports.exam.delete.my": { label: "Delete My Exam Reports", access: { role: [1, 4, 5], permissions: [] } },
                //         "reports.exam.view": { label: "View Exam Reports", access: { role: [1, 2, 3, 4, 5, 7], permissions: [] } },
                //         "reports.exam.view.my": { label: "View My Exam Reports", access: { role: [1, 4, 5, 6, 7], permissions: [] } },
                //         "reports.exam.download": { label: "Download Exam Reports", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "reports.exam.download.my": { label: "Download My Exam Reports", access: { role: [1, 4, 5, 6, 7], permissions: [] } },
                //         "reports.fee": { label: "Access Fee Reports", access: { role: [1, 2, 3, 4, 5, 7], permissions: [] } },
                //         "reports.fee.my": { label: "Access My Fee Reports", access: { role: [1, 6, 7], permissions: [] } },
                //         "reports.attendance": { label: "Access Attendance Reports", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "reports.attendance.my": { label: "Access My Attendance Reports", access: { role: [1, 6, 7], permissions: [] } },
                //         "reports.behaviour": { label: "Access Behaviour Reports", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "reports.behaviour.my": { label: "Access My Behaviour Reports", access: { role: [1, 6, 7], permissions: [] } },
                //         "reports.academic": { label: "Access Academic Reports", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "reports.academic.my": { label: "Access My Academic Reports", access: { role: [1, 6, 7], permissions: [] } },
                //         "reports.performance": { label: "Access Performance Reports", access: { role: [1, 2, 3, 4, 5], permissions: [] } },
                //         "reports.performance.my": { label: "Access My Performance Reports", access: { role: [1, 6, 7], permissions: [] } },
                //         "reports.student": { label: "Access Student Performance Reports", access: { role: [1, 2, 3, 4, 5], permissions: [] } }
                //     }
                // },
                "profile": {
                    "name": "Profile",
                    "access": { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] },
                    "permissions": {
                        "profile.view": { label: "View Profile", access: { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] } },
                        "profile.edit": { label: "Edit Profile", access: { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] } },
                        "profile.change_password": { label: "Change Password", access: { role: [1, 2, 3, 4, 5, 6, 7], permissions: [] } },
                        "profile.delete": { label: "Delete Profile", access: { role: [1, 2, 3], permissions: [] } },
                        "profile.delete.my": { label: "Delete My Profile", access: { role: [1, 4, 5, 6, 7], permissions: [] } }
                    }
                },
                "settings": {
                    "name": "Settings",
                    "access": { role: [1, 2, 3], permissions: ['settings.manage', 'settings.advanced'] },
                    "permissions": {
                        "settings.manage": { label: "Manage Settings", access: { role: [1, 2, 3], permissions: ['settings.manage'] } },
                        "settings.advanced": { label: "Access Advanced Settings", access: { role: [1], permissions: ['settings.advanced'] } }
                    }
                }
            };

            function getUserPermissionsFormat(staticData, pinnedUser) {

                const result = {};

                for (let moduleKey in staticData) {
                    const module = staticData[moduleKey];

                    // Check module-level access
                    if (module.access.role.includes(pinnedUser.role) ||
                        module.access.permissions.some(p => pinnedUser.permissions.includes(p))) {

                        result[moduleKey] = {
                            name: module.name,
                            permissions: {}
                        };

                        for (let permKey in module.permissions) {
                            const perm = module.permissions[permKey];

                            const hasRoleAccess = perm.access.role.includes(pinnedUser.role);
                            const hasPermissionAccess = perm.access.permissions.some(p => pinnedUser.permissions.includes(p));

                            if (hasRoleAccess || hasPermissionAccess) {
                                result[moduleKey].permissions[permKey] = perm.label;
                            }
                        }
                    }
                    // else {
                    //     result[moduleKey] = {
                    //         name: module.name,
                    //         permissions: {}
                    //     };

                    //     for (let permKey in module.permissions) {
                    //         const perm = module.permissions[permKey];
                    //         result[moduleKey].permissions[permKey] = perm.label;

                    //     }
                    // }
                }

                return result;
            }

            // Initialize permission modal scope
            $rootScope.initPermissionModal = function (group) {
                $scope.selectedGroup = group;
                $scope.selectedPermissions = {};
                $scope.permissionModules = [];
                $scope.isLoading = false;
                $scope.isSaving = false;

                $scope.selectedUserGroup = { role: group.id, permissions: [] };
                $scope.filteredPermissions = getUserPermissionsFormat($scope.staticPermissionData, $scope.selectedUserGroup);
                $scope.processStaticPermissionData();
                $scope.loadGroupCurrentPermissions();
            };

            $scope.processStaticPermissionData = function () {
                $scope.permissionModules = [];

                for (var moduleKey in $scope.filteredPermissions) {
                    if ($scope.filteredPermissions.hasOwnProperty(moduleKey)) {
                        var module = $scope.filteredPermissions[moduleKey];
                        var moduleObj = {
                            key: moduleKey,
                            name: module.name,
                            permissions: []
                        };

                        for (var permissionKey in module.permissions) {
                            if (module.permissions.hasOwnProperty(permissionKey)) {
                                moduleObj.permissions.push({
                                    key: permissionKey,
                                    name: module.permissions[permissionKey]
                                });
                            }
                        }

                        $scope.permissionModules.push(moduleObj);
                    }
                }

                // Force UI update
                $timeout(function () {
                    if (!$scope.$$phase) $scope.$apply();
                }, 10);
            };

            // Load current permissions for the selected group from API
            $scope.loadGroupCurrentPermissions = function () {
                if (!$scope.selectedGroup || !$scope.selectedGroup.id) {
                    console.error('No selected group or group ID');
                    return;
                }

                $http({
                    url: window.baseUrl + '/API/user_groups/' + $scope.selectedGroup.id + '/permissions',
                    method: 'GET'
                }).then(
                    function (response) {
                        const currentPermissions = response.data?.permissions || response.data || [];

                        // Initialize selectedPermissions object
                        $scope.selectedPermissions = {};

                        if (currentPermissions.length > 0) {
                            currentPermissions.forEach(permission => {
                                // Handle both string and object formats
                                const permissionKey = typeof permission === 'string' ? permission : (permission.key || permission);
                                $scope.selectedPermissions[permissionKey] = true;
                            });
                        } else {
                            return
                        }
                    },
                    function (error) {
                        console.error('Failed to load group permissions:', error);
                        // Initialize empty selectedPermissions even if API fails
                        $scope.selectedPermissions = {};
                    }
                );
            };

            // Save permissions to API
            $scope.savePermissions = function () {
                if (!$scope.selectedGroup || !$scope.selectedGroup.id) {
                    Toast.fire({
                        type: 'error',
                        title: 'Error!',
                        msg: 'No group selected'
                    });
                    return;
                }

                $scope.isSaving = true;
                const selectedPerms = Object.keys($scope.selectedPermissions).filter(key => $scope.selectedPermissions[key]);

                $http({
                    url: window.baseUrl + '/API/user_groups/' + $scope.selectedGroup.id + '/permissions',
                    method: 'PUT',
                    data: { permissions: selectedPerms }
                }).then(
                    function (response) {
                        $scope.isSaving = false;

                        Toast.fire({
                            type: 'success',
                            title: 'Success!',
                            msg: 'Permissions updated successfully'
                        });

                        Toast.popover({ type: 'close' });
                        $timeout(function () {
                            if ($scope.loadUserGroups) {
                                $scope.loadUserGroups();
                            }
                        }, 100);

                    },
                    function (error) {
                        $scope.isSaving = false;
                        const errorMsg = error.data?.message || 'Failed to update permissions';
                        console.error('Save error:', error);
                        Toast.fire({
                            type: 'error',
                            title: 'Error!',
                            msg: errorMsg
                        });
                    }
                );
            };

            // Select all permissions in a module
            $scope.selectAllInModule = function (module, isSelected) {
                module.permissions.forEach(permission => {
                    $scope.selectedPermissions[permission.key] = isSelected;
                });
            };

            // Check if all permissions in module are selected
            $scope.isModuleAllSelected = function (module) {
                return module.permissions.every(permission => $scope.selectedPermissions[permission.key]);
            };

            // Check if module has some permissions selected
            $scope.isModulePartialSelected = function (module) {
                const selectedCount = module.permissions.filter(permission => $scope.selectedPermissions[permission.key]).length;
                return selectedCount > 0 && selectedCount < module.permissions.length;
            };

            // Public API
            return {
                init: function (group) {
                    $scope.initPermissionModal(group);
                },
                close: function () {
                    Toast.popover({ type: 'close' });
                },
                save: function () {
                    $scope.savePermissions();
                }
            };
        };
    }
]);

// user group create/edit modal controller
app.factory("createAndEdituserGroupModalController", [
    "API_URL",
    "window",
    "jQuery",
    "$http",
    "$sce",
    "$rootScope",
    "$compile",
    "$timeout",
    function (
        API_URL,
        window,
        $,
        $http,
        $sce,
        $rootScope,
        $compile,
        $timeout
    ) {
        return function ($scope) {

            $scope.initCreateAndEdituserGroupModal = function (isEditing, group = null) {
                $scope.isEditing = isEditing;
                if (isEditing && group) {
                    $scope.group = angular.copy(group);
                } else {
                    $scope.group = {
                        name: '',
                        description: ''
                    };
                }
            };

            // Correct save
            $scope.groupSave = function () {
                $scope.isSaving = true;

                const apiUrl = $scope.isEditing
                    ? `${window.baseUrl}/API/user_groups/${$scope.group.id}`
                    : window.baseUrl + '/API/user_groups';
                const method = $scope.isEditing ? 'POST' : 'POST';

                $http({
                    method: method,
                    url: apiUrl,
                    data: $('#user-group-create-and-edit-form').serialize(),
                }).then(function (response) {
                    $scope.isSaving = false;
                    if (response.data && response.data.status === 'success') {
                        $scope.closeModal();
                        $scope.loadUserGroups();
                        Toast.fire({ type: "success", title: "Success!", msg: $scope.isEditing ? "User group updated successfully" : "User group created successfully" });
                    } else {
                        Toast.fire({ type: "error", title: "Error!", msg: $scope.isEditing ? "Failed to update user group" : "Failed to create user group" });
                    }
                }, function (error) {
                    $scope.isSaving = false;
                    Toast.fire({ type: "error", title: "Error!", msg: $scope.isEditing ? "Failed to update user group" : "Failed to create user group" });
                    console.error("Error saving user group:", error);
                });
            };

            return {
                init: function (isEditing, group = null) {
                    $scope.initCreateAndEdituserGroupModal(isEditing, group);
                },
                close: function () {
                    Toast.popover({ type: "close" });
                },
                save: function () {
                    $scope.groupSave();
                }
            };
        };
    }
]);

// Delete user group modal controller
app.factory("deleteUserGroupModalController", [
    "API_URL",
    "window",
    "jQuery",
    "$http",
    "$sce",
    "$rootScope",
    "$compile",
    "$timeout",
    function (
        API_URL,
        window,
        $,
        $http,
        $sce,
        $rootScope,
        $compile,
        $timeout
    ) {
        return function ($scope) {

            $scope.initDeleteUserGroupModal = function (group) {
                $scope.groupToDelete = group;
            };

            $scope.deleteUserGroup = function () {
                $http({
                    url: window.baseUrl + '/API/user_groups/' + $scope.groupToDelete.id,
                    method: 'DELETE'
                }).then(
                    function (response) {
                        Toast.fire({
                            type: 'success',
                            title: 'Success!',
                            msg: 'Group deleted successfully'
                        });
                        $scope.closeDeleteModal();
                        $scope.loadUserGroups();
                    },
                    function (error) {
                        const errorMsg = error.data?.message || 'Failed to delete group';
                        Toast.fire({
                            type: 'error',
                            title: 'Error!',
                            msg: errorMsg
                        });
                    }
                );
            };

            $scope.closeDeleteModal = function () {
                Toast.popover({ type: 'close' });
            };

            return {
                init: function (group) {
                    $scope.initDeleteUserGroupModal(group);
                },
                close: function () {
                    Toast.popover({ type: "close" });
                }
            };
        };
    }
]);

// Section editor modal controller
app.factory("sectionEditorModalController", [
    "API_URL",
    "window",
    "jQuery",
    "$http",
    "$sce",
    "$rootScope",
    "$compile",
    "$timeout",
    function (
        API_URL,
        window,
        $,
        $http,
        $sce,
        $rootScope,
        $compile,
        $timeout
    ) {
        return function ($scope) {

            const save = async function () {
                if (!$scope.currentSection.title) {
                    Toast.fire({
                        type: 'error',
                        title: 'Validation Error!',
                        msg: 'Please enter the section title'
                    });
                    return;
                }

                if (!$scope.currentSection.question_count) {
                    Toast.fire({
                        type: 'error',
                        title: 'Validation Error!',
                        msg: 'Please enter the number of questions'
                    });
                    return;
                }

                const endpoint = $scope.currentSection.id ? window.baseUrl + '/API/sections/edit/' + $scope.currentSection.id : window.baseUrl + '/API/sections/add';
                await $http({
                    url: endpoint,
                    method: 'POST',
                    data: $('#section_form').serialize()
                }).then(function (response) {
                    if (response.data.status === 'success') {
                        $scope.currentSection = response.data.section;
                        if ($scope.editingSectionIndex === null) {
                            $scope.savedSections.push(angular.copy(response.data.section));
                            Toast.fire({
                                type: 'success',
                                title: 'Success!',
                                msg: 'Section created successfully'
                            });
                        } else {
                            // Update existing section
                            $scope.savedSections[$scope.editingSectionIndex] = angular.copy(response.data.section);
                            Toast.fire({
                                type: 'success',
                                title: 'Success!',
                                msg: 'Section updated successfully'
                            });
                        }

                        $scope.currentSection = {};
                        $scope.editingSectionIndex = null;
                        $scope.closeSectionEditorModal()
                    }
                })
                $scope.showSectionModal = false;
                $scope.showSecondDescription = false;
                $scope.updateBaseDatas();
                $scope.updateSectionQuestionCounts();
                $scope.$apply();
            };

            $scope.closeSectionEditorModal = () => {
                Toast.popover({ type: 'close' })
            }

            $scope.saveSection = () => {
                save();
            }

            return {
                save: function () {
                    save();
                },
                close: function () {
                    closeSectionEditorModal();
                }
            };
        };
    }
]);

// Assign to section modal controller
app.factory("assignToSectionModalController", [
    "API_URL",
    "window",
    "jQuery",
    "$http",
    "$sce",
    "$rootScope",
    "$compile",
    "$timeout",
    function (
        API_URL,
        window,
        $,
        $http,
        $sce,
        $rootScope,
        $compile,
        $timeout
    ) {
        return function ($scope) {

            const initAssigningToSectionModal = (questionId) => {
                $scope.assignSectionId = null;
                $scope.currentQuestionId = questionId;
            }

            // Confirm Assignment
            const assignToSection = () => {
                const sectionId = parseInt($scope.assignSectionId);
                const questionId = parseInt($scope.currentQuestionId);

                const section = $scope.savedSections.find(s => s.id === sectionId);
                const question = $scope.savedQuestions.find(q => q.id === questionId);

                // Section full?
                if (section.assignedQuestions >= section.question_count) {
                    Toast.fire({
                        type: 'error',
                        title: 'Error!',
                        msg: 'This section reached its limit (' + section.question_count + ' questions)'
                    });
                    return;
                }

                // Ensure array exists
                if (!Array.isArray(question.assignedSections)) {
                    question.assignedSections = [];
                }

                // Already assigned check
                if (question.assignedSections.includes(sectionId)) {
                    Toast.fire({
                        type: 'info',
                        title: 'Info',
                        msg: 'Question already assigned to this section'
                    });
                    $scope.showAssignModal = false;
                    return;
                }

                // API CALL
                $http({
                    url: window.baseUrl + '/API/questions/assign_to_section/' + questionId,
                    method: 'POST',
                    data: $('#assign_question_to_section_form').serialize()
                }).then(function (response) {

                    if (response.data.status === 'success') {

                        question.assignedSections.push(sectionId);

                        const qIndex = $scope.savedQuestions.findIndex(q => q.id === questionId);
                        $scope.savedQuestions[qIndex] = angular.copy(question);

                        $scope.updateSectionQuestionCounts();
                        $scope.updateBaseDatas();
                        $scope.closePopover();

                        Toast.fire({
                            type: 'success',
                            title: 'Success!',
                            msg: 'Question assigned successfully'
                        });

                        $scope.showAssignModal = false;

                    } else {
                        Toast.fire({
                            type: 'error',
                            title: 'Error',
                            msg: response.data.msg || 'Failed to assign question'
                        });
                    }

                }, function (error) {
                    Toast.fire({
                        type: 'error',
                        title: 'Error',
                        msg: error.data.msg || 'Failed to assign question'
                    });
                });

            };

            $scope.confirmAssignToSection = () => {
                assignToSection();
            }

            $scope.cancelAssignToSection = () => {
                Toast.popover({ type: "close" });
                $scope.showAssignModal = false;
            }

            return {
                init: function (questionID) {
                    initAssigningToSectionModal(questionID);
                },
                confirmAssigning: function () {
                    assignToSection();
                },
                close: function () {
                    Toast.popover({ type: "close" });
                }
            };
        };
    }
]);

// Unassign from section modal controller
app.factory("unassignFromSectionModalController", [
    "API_URL",
    "window",
    "jQuery",
    "$http",
    "$sce",
    "$rootScope",
    "$compile",
    "$timeout",
    function (
        API_URL,
        window,
        $,
        $http,
        $sce,
        $rootScope,
        $compile,
        $timeout
    ) {
        return function ($scope) {

            const initAssigningToSectionModal = (questionID) => {
                const question = $scope.savedQuestions.find(q => q.id === questionID);

                if (!question) {
                    console.error("Question not found");
                    return;
                }

                // Ensure assignedSections exists
                if (!Array.isArray(question.assignedSections)) {
                    question.assignedSections = [];
                }

                // Filter only those sections assigned to this question
                $scope.selectedQuestionAssignedSections = $scope.savedSections
                    .filter(section => question.assignedSections.includes(section.id));

                // Save question ID
                $scope.unassignQuestion = questionID;
            }

            // Confirm Unassignment
            const unassignSection = () => {
                const questionId = $scope.unassignQuestion;

                // Get the question
                const question = $scope.savedQuestions.find(q => q.id === questionId);
                if (!question) {
                    Toast.fire({ type: 'error', title: 'Error', msg: 'Question not found' });
                    return;
                }

                // Ensure list exists
                if (!Array.isArray(question.assignedSections) || question.assignedSections.length === 0) {
                    Toast.fire({ type: 'error', title: 'Error', msg: 'This question is not assigned to any section' });
                    return;
                }

                // Selected section ID to unassign
                const sectionId = parseInt($scope.unassignSectionId);

                if (!sectionId) {
                    Toast.fire({ type: 'error', title: 'Error', msg: 'Please select a section' });
                    return;
                }

                // Check if assigned
                if (!question.assignedSections.includes(sectionId)) {
                    Toast.fire({ type: 'info', title: 'Info', msg: 'This question is not assigned to selected section' });
                    return;
                }

                $http({
                    url: window.baseUrl + '/API/questions/unassign_section/' + questionId,
                    method: 'POST',
                    data: $('#remove_question_to_section_form').serialize()
                }).then(function (response) {

                    if (response.data.status === 'success') {
                        // Remove sectionId from assignedSections array
                        question.assignedSections = question.assignedSections.filter(id => id !== sectionId);

                        // Update savedQuestions UI list
                        const qIndex = $scope.savedQuestions.findIndex(q => q.id === questionId);
                        $scope.savedQuestions[qIndex] = angular.copy(question);

                        // Update section counts
                        $scope.updateSectionQuestionCounts();
                        $scope.updateBaseDatas();
                        $scope.closePopover();
                        Toast.fire({
                            type: 'success',
                            title: 'Success',
                            msg: 'Section unassigned successfully'
                        });

                        $scope.showUnassignModal = false;

                    } else {
                        Toast.fire({ type: 'error', title: 'Error', msg: 'Failed to unassign section' });
                    }

                    $scope.showUnasignSectionModal = false;

                }, function () {
                    Toast.fire({ type: 'error', title: 'Error', msg: 'Failed to unassign section' });
                });
            };

            const closeUnassingModal = () => {
                Toast.popover({ type: "close" });
                $scope.showUnassignModal = false;
                $scope.selectedQuestionAssignedSections = null;
                $scope.unassignQuestion = null;
            }

            $scope.confirmUnassignSection = () => {
                unassignSection();
            }

            $scope.closeUnassingModal = () => {
                closeUnassingModal();
            }

            return {
                init: function (questionID) {
                    initAssigningToSectionModal(questionID);
                },
                confirmUnassigning: function () {
                    unassignSection();
                },
                close: function () {
                    closeUnassingModal();
                }
            };
        };
    }
]);

// Question edior modal controller
app.factory('questionEditorModalController', [
    "API_URL",
    "window",
    "jQuery",
    "$http",
    "$sce",
    "$rootScope",
    "$compile",
    "$timeout",
    function (
        API_URL,
        window,
        $,
        $http,
        $sce,
        $rootScope,
        $compile,
        $timeout
    ) {
        return function ($scope) {

            const initQuestionEditorModal = (question) => {
                question.correctAnswer = question.correctAnswer.toUpperCase();
                $scope.currentQuestion = question;
            }

            const save = async function (id) {
                if (!$scope.currentQuestion.question) {
                    Toast.fire({ type: 'error', title: 'Validation Error!', msg: 'Please enter question text' });
                    return false;
                }

                const validOptions = $scope.currentQuestion.options.filter(opt => opt.text || opt.image);
                if (validOptions.length < 4) {
                    Toast.fire({ type: 'error', title: 'Validation Error!', msg: 'All options are required' });
                    return false;
                }

                if ($scope.currentQuestion.correctAnswer === null || $scope.currentQuestion.correctAnswer === undefined) {
                    Toast.fire({ type: 'error', title: 'Validation Error!', msg: 'Please select a correct answer' });
                    return false;
                }

                // API URI
                const apiUrl = window.baseUrl + '/API/questions/edit_question/' + $scope.currentQuestion.id;
                const formData = $(`#questionForm${$scope.currentQuestion.id}`).serialize();
                try {
                    const response = await $http({
                        url: apiUrl,
                        data: formData,
                        method: 'POST',
                    });

                    if (response.data.status === 'success') {
                        Toast.popover({ type: 'close' })
                        Toast.fire({ type: 'success', title: 'Success!', msg: response.data.msg || 'Question updated successfully' });

                        let updated = response.data.question;
                        updated.correctAnswer = updated.answer;

                        // Update currentQuestion
                        $scope.currentQuestion = updated;
                        $scope.currentQuestionIndex = $scope.allQuestions.findIndex(q => q.id === updated.id);
                        $scope.allQuestions[$scope.currentQuestionIndex] = angular.copy(updated);
                        $scope.currentQuestion = null
                    } else {
                        Toast.fire({ type: 'error', title: 'Error!', msg: response.data.msg });
                    }
                } catch (error) {
                    Toast.fire({ type: 'error', title: 'Error!', msg: 'Something went wrong. Failed to update the question.' });
                    console.error(error);
                };
            };

            $scope.closeSectionEditorModal = () => {
                Toast.popover({ type: 'close' })
            }

            $scope.saveSection = () => {
                save();
            }

            return {
                init: function (question) {
                    initQuestionEditorModal(question);
                },
                save: function () {
                    save();
                },
                close: function () {
                    closeSectionEditorModal();
                }
            };
        };
    }
]);