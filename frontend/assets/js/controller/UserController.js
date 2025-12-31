app.controller("UserController", [
    "$scope",
    "$http",
    "$compile",
    "$timeout",
    "$document",
    "permissionModalController",
    "createAndEdituserGroupModalController",
    "deleteUserGroupModalController",
    function (
        $scope,
        $http,
        $compile,
        $timeout,
        $document,
        permissionModalController,
        createAndEdituserGroupModalController,
        deleteUserGroupModalController
    ) {
        // Initialize variables
        $scope.loading = true;
        $scope.error = null;
        $scope.userGroups = [];
        $scope.activeGroupMenu = null;
        $scope.users = [];
        $scope.filteredUsers = [];
        $scope.selectedGroup = "";
        $scope.selectedStatus = "";
        $scope.searchTerm = "";
        $scope.userGroupDropdownOpen = false;
        $scope.statusDropdownOpen = false;

        // Sorting
        $scope.sortColumn = "name";
        $scope.sortReverse = false;

        // Pagination
        $scope.currentPage = 1;
        $scope.pageSizeOptions = [5, 10, 25, 50, 100, "All"];
        $scope.pageSize = 10;
        $scope.totalUsers = 0;
        $scope.totalPages = 0;

        // Initialize permission modal controller
        $scope.permissionModalCtrl = null;

        // Initialize controller
        $scope.init = function () {
            $scope.loadUserGroups();
            $scope.loadLoggedUserData();
            $scope.permissionModalCtrl = permissionModalController($scope);
            $scope.createAndEdituserGroupModalCtrl =
                createAndEdituserGroupModalController($scope);
            $scope.deleteUserGroupModalCtrl = deleteUserGroupModalController($scope);
        };

        // Load user groups on controller initialization
        $scope.loadUserGroups = function () {
            $scope.loading = true;
            $scope.error = null;

            $http({
                url: window.baseUrl + "/API/user_groups",
                method: "GET",
            }).then(
                function (response) {
                    $scope.loading = false;
                    if (response.data && response.data.success) {
                        $scope.userGroups = response.data.data || [];
                    } else {
                        $scope.userGroups = response.data || [];
                    }
                },
                function (error) {
                    $scope.loading = false;
                    $scope.error =
                        error.data?.message || "Failed to load user groups from API";
                    console.error("API Error:", error);

                    Toast.fire({
                        type: "error",
                        title: "Error!",
                        msg: $scope.error,
                    });
                }
            );
        };

        // Load available permissions from API
        $scope.loadPermissions = function () {
            $http({
                url: window.baseUrl + "/API/permissions",
                method: "GET",
            }).then(
                function (response) {
                    if (response.data && response.data.success) {
                        $scope.permissionModules = response.data.data;
                    } else {
                        $scope.permissionModules = response.data;
                    }
                },
                function (error) {
                    console.error("Failed to load permissions:", error);
                }
            );
        };

        // Fetch logged user data
        $scope.loadLoggedUserData = function () {
            $http({
                url: window.baseUrl + "/API/auth/logged_user",
                method: "GET",
            }).then(
                function (response) {
                    $scope.theLoggedUser = response.data;
                },
                function (error) {
                    Toast.fire({
                        type: "error",
                        title: "Error!",
                        msg: "Failed to fetch logged user data. Please refresh the page.",
                    });
                    console.error("Failed to fetch looged user data:", error);
                }
            );
        };

        // Toggle group menu
        $scope.toggleGroupMenu = function (groupId) {
            $scope.activeGroupMenu =
                $scope.activeGroupMenu === groupId ? null : groupId;
        };

        // Create new group
        $scope.createGroup = function () {
            if (!$scope.createAndEdituserGroupModalCtrl) {
                $scope.createAndEdituserGroupModalCtrl =
                    createAndEdituserGroupModalController($scope);
            }

            let isEditing = false;
            $scope.createAndEdituserGroupModalCtrl.init(isEditing);

            Toast.popover({
                type: "content",
                title: "Create User Group",
                apiConfig: {
                    endpoint: "create_user_group",
                    method: "GET",
                },
                size: "lg",
                buttons: [
                    {
                        text: "Cancel",
                        background: "#F44336",
                        onClick: function () {
                            $scope.createAndEdituserGroupModalCtrl.close();
                        },
                    },
                    {
                        text: "Create Group",
                        background: "#4CAF50",
                        onClick: function () {
                            $scope.createAndEdituserGroupModalCtrl.save();
                        },
                    },
                ],
                buttonPosition: "around",
                buttonWidth: "full",
                buttonContainerStyles: "padding-bottom: 1.5rem;",
            }).then((popoverInstance) => {
                $timeout(() => {
                    const modal = document.getElementById("create-user-group-modal");
                    if (modal) {
                        $compile(modal)($scope);
                        $scope.$apply();
                    } else {
                        console.error("#create-user-group-modal not found");
                    }
                }, 150);
            });
        };

        // Edit group
        $scope.editGroup = function (group) {
            if (!$scope.createAndEdituserGroupModalCtrl) {
                $scope.createAndEdituserGroupModalCtrl =
                    createAndEdituserGroupModalController($scope);
            }

            let isEditing = true;
            $scope.createAndEdituserGroupModalCtrl.init(isEditing, group);

            Toast.popover({
                type: "content",
                title: "Edit User Group",
                apiConfig: {
                    endpoint: "create_user_group",
                    method: "GET",
                },
                size: "lg",
                buttons: [
                    {
                        text: "Cancel",
                        background: "#F44336",
                        onClick: function () {
                            $scope.createAndEdituserGroupModalCtrl.close();
                        },
                    },
                    {
                        text: "Save Changes",
                        background: "#4CAF50",
                        onClick: function () {
                            $scope.createAndEdituserGroupModalCtrl.save();
                        },
                    },
                ],
                buttonPosition: "around",
                buttonWidth: "full",
                buttonContainerStyles: "padding-bottom: 1.5rem;",
            }).then((popoverInstance) => {
                $timeout(() => {
                    const modal = document.getElementById("create-user-group-modal");
                    if (modal) {
                        $compile(modal)($scope);
                        $scope.$apply();
                    } else {
                        console.error("#create-user-group-modal not found");
                    }
                }, 150);
            });
        };

        $scope.setPermissions = function (group) {
            $scope.selectedGroup = group;
            if (!$scope.permissionModalCtrl) {
                $scope.permissionModalCtrl = permissionModalController($scope);
            }

            // Init permission modules before popover opens
            $scope.permissionModalCtrl.init(group);

            Toast.popover({
                type: "apiContent",
                title: "Set Permissions for " + group.name,
                apiConfig: {
                    endpoint: "permission",
                    method: "GET",
                },
                size: "xxl",
                buttons: [
                    {
                        text: "Save Permissions",
                        onClick: function () {
                            $scope.permissionModalCtrl.save();
                        },
                    },
                ],
                buttonPosition: "end",
            }).then((popoverInstance) => {
                $timeout(() => {
                    const modal = document.getElementById("permission-model");
                    if (modal) {
                        $compile(modal)($scope);
                        $scope.$apply();
                    } else {
                        console.error("#permission-model not found");
                    }
                }, 150);
            });
        };

        // Delete group
        $scope.deleteGroup = function (group) {
            if (!$scope.deleteUserGroupModalCtrl) {
                $scope.deleteUserGroupModalCtrl =
                    deleteUserGroupModalController($scope);
            }
            $scope.deleteUserGroupModalCtrl.init(group);

            Toast.popover({
                type: "content",
                title: "Delete Confirmation",
                apiConfig: {
                    endpoint: "delete_user_group",
                    method: "GET",
                },
                size: "md",
            }).then((popoverInstance) => {
                $timeout(() => {
                    const modal = document.getElementById(
                        "confirm-user-group-delete-modal"
                    );
                    if (modal) {
                        $compile(modal)($scope);
                        $scope.$apply();
                    } else {
                        console.error("#confirm-user-group-delete-modal not found");
                    }
                }, 150);
            });
        };

        // View members
        $scope.viewMembers = function (group) {
            $http({
                url: window.baseUrl + "/API/user-groups/" + group.id + "/members",
                method: "GET",
            }).then(
                function (response) {
                    const members = response.data?.data || response.data || [];
                    let content = "";

                    if (members.length > 0) {
                        content = `
                            <div class="max-h-96 overflow-y-auto">
                                <div class="space-y-2">
                                    ${members
                                .map(
                                    (member) => `
                                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                            <div>
                                                <p class="font-medium">${member.name || member.email
                                        }</p>
                                                <p class="text-sm text-gray-500">${member.email
                                        }</p>
                                            </div>
                                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">${member.role || "Member"
                                        }</span>
                                        </div>
                                    `
                                )
                                .join("")}
                                </div>
                            </div>
                        `;
                    } else {
                        content = `
                            <div class="text-center py-8">
                                <i class="fas fa-users text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-500">No members in this group</p>
                            </div>
                        `;
                    }

                    Toast.popover({
                        type: "content",
                        title: "Members of " + group.name,
                        content: content,
                        size: "md",
                        buttons: [
                            {
                                text: "Close",
                                class: "popover-button-primary",
                                onClick: function () {
                                    popover.destroyAll();
                                },
                            },
                        ],
                    });
                },
                function (error) {
                    Toast.fire({
                        type: "error",
                        title: "Error!",
                        msg: "Failed to load group members",
                    });
                }
            );
        };

        // Close modals
        $scope.closeModal = $scope.closeDeleteModal = function () {
            Toast.popover({ type: "close" });
        };

        // Initialize the controller
        $scope.init();

        // Load users from API
        $scope.loadUsers = function () {
            $scope.loading = true;

            $http
                .get(window.baseUrl + "/API/users", {
                    params: {
                        filter: $scope.selectedGroup || null,
                        status: $scope.selectedStatus || null,
                    },
                })
                .then(
                    function (response) {
                        $scope.loading = false;
                        $scope.users = response.data || [];

                        // Immediately apply filter after loading
                        $scope.applyFilter();
                    },
                    function (error) {
                        $scope.loading = false;
                        $scope.users = [];
                        $scope.filteredUsers = [];
                        $scope.totalPages = 0;
                        console.error(error);
                    }
                );
        };

        $scope.sortBy = function (column) {
            if ($scope.sortColumn === column) {
                $scope.sortReverse = !$scope.sortReverse;
            } else {
                $scope.sortColumn = column;
                $scope.sortReverse = false;
            }
            $scope.applySorting();
        };

        $scope.applySorting = function () {
            $scope.filteredUsers.sort((a, b) => {
                let valA = a[$scope.sortColumn];
                let valB = b[$scope.sortColumn];

                // Convert to lowercase if string
                if (typeof valA === "string") valA = valA.toLowerCase();
                if (typeof valB === "string") valB = valB.toLowerCase();

                if (valA < valB) return $scope.sortReverse ? 1 : -1;
                if (valA > valB) return $scope.sortReverse ? -1 : 1;
                return 0;
            });
        };

        // Apply search + group + status filter (client-side)
        $scope.applyFilter = function () {
            if (!$scope.users) return;

            let term = ($scope.searchTerm || "").toLowerCase();

            $scope.filteredUsers = $scope.users.filter((user) => {
                let matchesName = user.name.toLowerCase().includes(term);
                let matchesEmail = user.email.toLowerCase().includes(term);
                let matchesGroup = $scope.selectedGroup
                    ? user.user_group == $scope.selectedGroup
                    : true;
                let matchesStatus = $scope.selectedStatus
                    ? user.status.toLowerCase() == $scope.selectedStatus.toLowerCase()
                    : true;

                return (matchesName || matchesEmail) && matchesGroup && matchesStatus;
            });

            // Apply sorting after filtering
            $scope.applySorting();

            $scope.currentPage = 1;
            $scope.totalUsers = $scope.filteredUsers.length;
            $scope.calculateTotalPages();
        };

        // Call applyFilter() whenever searchTerm, selectedGroup or selectedStatus changes
        $scope.filterUsers = function () {
            $scope.applyFilter();
        };

        // Calculate total pages based on page size
        $scope.calculateTotalPages = function () {
            if ($scope.pageSize === "All") {
                $scope.totalPages = 1;
            } else {
                $scope.totalPages = Math.ceil($scope.totalUsers / $scope.pageSize);
            }
        };

        // Pagination helpers
        $scope.getPages = function () {
            let pages = [];
            for (let i = 1; i <= $scope.totalPages; i++) pages.push(i);
            return pages;
        };

        $scope.goToPage = function (page) {
            if (page >= 1 && page <= $scope.totalPages) $scope.currentPage = page;
        };

        $scope.previousPage = function () {
            if ($scope.currentPage > 1) $scope.currentPage--;
        };

        $scope.nextPage = function () {
            if ($scope.currentPage < $scope.totalPages) $scope.currentPage++;
        };

        $scope.getRangeEnd = function () {
            if ($scope.pageSize === "All") {
                return $scope.totalUsers;
            }
            return Math.min($scope.currentPage * $scope.pageSize, $scope.totalUsers);
        };

        $scope.changePageSize = function () {
            let size = document.getElementById("pageSizeSelect").value;

            if (size !== "All") size = Number(size);
            $scope.pageSize = size;
            $scope.currentPage = 1;
            $scope.totalPages =
                size === "All" ? 1 : Math.ceil($scope.totalUsers / size);

            // Angular digest
            if (!$scope.$$phase) $scope.$apply();
        };

        $scope.paginatedUsers = function () {
            if (!$scope.filteredUsers) return [];
            if ($scope.pageSize === "All") return $scope.filteredUsers;

            const size =
                $scope.pageSize === "All" ? $scope.totalUsers : Number($scope.pageSize);
            const start = ($scope.currentPage - 1) * size;
            return $scope.filteredUsers.slice(start, start + size);
        };

        // Initial load
        $scope.loadUsers();

        // Add User Page Functions
        $scope.userData = {
            name: "",
            email: "",
            phone: "",
            username: "",
            user_group: "",
            status: "active",
            password: "",
            confirmPassword: "",
            department: "",
            position: "",
            notes: "",
        };

        $scope.loading = false;
        $scope.passwordVisible = {
            password: false,
            confirmPassword: false,
        };

        // Toggle password visibility
        $scope.togglePasswordVisibility = function (field) {
            $scope.passwordVisible[field] = !$scope.passwordVisible[field];
            const input = document.getElementById(field);
            if (input) {
                input.type = $scope.passwordVisible[field] ? "text" : "password";
            }
        };

        $scope.loading = false;

        // ---------------- Validation Functions ----------------
        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(String(email).toLowerCase());
        }

        function validatePhone(phone) {
            const re = /^\+?[0-9]{7,15}$/;
            return re.test(String(phone));
        }

        function validateUsername(username) {
            const re = /^[a-zA-Z0-9_]{3,20}$/;
            return re.test(String(username));
        }

        function validatePassword(password) {
            return password && password.length >= 3;
        }

        // ---------------- Helper to safely set field errors ----------------
        function setFieldError(fieldName, errorName, value) {
            if (!$scope.addUserForm[fieldName]) $scope.addUserForm[fieldName] = {};
            if (!$scope.addUserForm[fieldName].$error === undefined)
                $scope.addUserForm[fieldName].$error = {};
            $scope.addUserForm[fieldName].$error[errorName] = value;
        }

        // ---------------- Reset all errors ----------------
        function resetFormErrors() {
            const fields = [
                "fullName",
                "email",
                "phone",
                "username",
                "password",
                "confirmPassword",
                "userGroup",
                "status",
            ];
            fields.forEach((field) => {
                if (!$scope.addUserForm[field]) $scope.addUserForm[field] = {};
                $scope.addUserForm[field].$error = {};
            });
        }

        // ---------------- Real-time validation ----------------
        $scope.$watchGroup(
            ["userData.password", "userData.confirmPassword"],
            function ([pass, confirmPass]) {
                if ($scope.addUserForm && $scope.addUserForm.confirmPassword) {
                    $scope.addUserForm.confirmPassword.$error.passwordMatch =
                        pass && confirmPass && pass !== confirmPass;
                }
            }
        );

        $scope.$watch("userData.email", function (newVal) {
            if ($scope.addUserForm && $scope.addUserForm.email) {
                $scope.addUserForm.email.$error.email = newVal
                    ? !validateEmail(newVal)
                    : false;
            }
        });

        $scope.$watch("userData.phone", function (newVal) {
            if ($scope.addUserForm && $scope.addUserForm.phone) {
                $scope.addUserForm.phone.$error.phone = newVal
                    ? !validatePhone(newVal)
                    : false;
            }
        });

        $scope.$watch("userData.username", function (newVal) {
            if ($scope.addUserForm && $scope.addUserForm.username) {
                $scope.addUserForm.username.$error.username = newVal
                    ? !validateUsername(newVal)
                    : false;
            }
        });

        $scope.$watch("userData.password", function (newVal) {
            if ($scope.addUserForm && $scope.addUserForm.password) {
                $scope.addUserForm.password.$error.minlength = newVal
                    ? !validatePassword(newVal)
                    : false;
            }
        });

        // ---------------- Form Validation ----------------
        function validateForm() {
            let valid = true;
            resetFormErrors();

            // Full Name
            if (!$scope.userData.name) {
                valid = false;
                setFieldError("fullName", "required", true);
            }

            // Email
            if (!$scope.userData.email) {
                valid = false;
                setFieldError("email", "required", true);
            } else if (!validateEmail($scope.userData.email)) {
                valid = false;
                setFieldError("email", "email", true);
            }

            // Phone
            if (!$scope.userData.phone) {
                valid = false;
                setFieldError("phone", "required", true);
            } else if (!validatePhone($scope.userData.phone)) {
                valid = false;
                setFieldError("phone", "phone", true);
            }

            // Username
            if (!$scope.userData.username) {
                valid = false;
                setFieldError("username", "required", true);
            } else if (!validateUsername($scope.userData.username)) {
                valid = false;
                setFieldError("username", "username", true);
            }

            // User Group
            if (!$scope.userData.user_group) {
                valid = false;
                setFieldError("userGroup", "required", true);
            }

            // Status
            if (!$scope.userData.status) {
                valid = false;
                setFieldError("status", "required", true);
            }

            // Password
            if (!$scope.userData.password) {
                valid = false;
                setFieldError("password", "required", true);
            } else if (!validatePassword($scope.userData.password)) {
                valid = false;
                setFieldError("password", "minlength", true);
            }

            // Confirm Password
            if (!$scope.userData.confirmPassword) {
                valid = false;
                setFieldError("confirmPassword", "required", true);
            } else if ($scope.userData.password !== $scope.userData.confirmPassword) {
                valid = false;
                setFieldError("confirmPassword", "passwordMatch", true);
            }

            // User Group & Status
            if (!$scope.userData.user_group) valid = false;
            if (!$scope.userData.status) valid = false;

            return valid;
        }

        // ---------------- Submit Form ----------------
        $scope.submitUser = function () {
            $scope.addUserForm.submitted = true;

            if (!validateForm()) {
                Toast.fire({
                    type: "error",
                    title: "Validation Error!",
                    msg: "Please fill all required fields correctly.",
                });
                return;
            }

            $scope.loading = true;

            const submitData = $("#add-user-form").serialize();

            $http({
                url: window.baseUrl + "/API/user",
                method: "POST",
                data: submitData,
            }).then(
                function (response) {
                    $scope.loading = false;

                    if (response.data.status === "success") {
                        Toast.fire({
                            type: "success",
                            title: "Success!",
                            msg: "User created successfully",
                        });
                        $scope.resetForm();
                        // $timeout(() => window.location.href = 'user_management', 2000);
                    } else if (response.data.status === "success") {
                        Toast.fire({
                            type: "warning",
                            title: "Warning!",
                            msg: response.data.msg || 'User created successfully, but failed to send email. Please inform the user to reset their password manually.',
                        });
                        $scope.resetForm();
                    } else {
                        Toast.fire({
                            type: "error",
                            title: "Error!",
                            msg: response.data?.message || "Failed to create user",
                        });
                    }
                },
                function (error) {
                    $scope.loading = false;
                    Toast.fire({
                        type: "error",
                        title: "Error!",
                        msg: error.data?.message || "Failed to create user",
                    });
                    console.error("API Error:", error);
                }
            );
        };

        // Reset form
        $scope.resetForm = function () {
            $scope.userData = {
                name: "",
                email: "",
                phone: "",
                username: "",
                user_group: "",
                status: "active",
                password: "",
                confirmPassword: "",
                department: "",
                position: "",
                notes: "",
            };
            $scope.addUserForm.$setPristine();
            $scope.addUserForm.$setUntouched();
            $scope.addUserForm.$submitted = false;
        };

        // Close dropdowns when clicking outside
        $document.on("click", function (event) {
            const userGroupEl = document.querySelector(".user-group-dropdown");
            const statusEl = document.querySelector(".status-dropdown");

            if (userGroupEl && !userGroupEl.contains(event.target)) {
                $scope.$apply(() => ($scope.dropdownOpen = false));
            }

            if (statusEl && !statusEl.contains(event.target)) {
                $scope.$apply(() => ($scope.statusDropdownOpen = false));
            }
        });

        if (window.location.pathname.includes("users")) {
            $scope.loadUsers();
        } else {
            $scope.init();
        }

        $scope.sendMail = function () {
            $http.post(window.baseUrl + "/API/test-mail")
                .then((response) => {
                    if (response.data.status === "success") {
                        Toast.fire({
                            type: "success",
                            title: "Mail Send",
                            msg: "Reset password mail send successfully",
                        });
                    } else {
                        Toast.fire({
                            type: "error",
                            title: "Failed",
                            msg: "Failed to send reset password mail",
                        });
                    }
                });
        };
    },
]);
