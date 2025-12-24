app.controller('ProfileController', ['$scope', '$http', '$timeout', '$filter', function ($scope, $http, $timeout, $filter) {
    $scope.user = {};
    $scope.profileData = {};
    $scope.editing = false;
    $scope.saving = false;
    $scope.errors = {};
    $scope.passwordData = {
        current_password: '',
        new_password: '',
        confirm_password: ''
    };
    $scope.passwordStrength = 0;
    $scope.passwordRequirements = {
        length: false,
        uppercase: false,
        lowercase: false,
        number: false
    };
    $scope.changingPassword = false;
    $scope.activeSessions = [];
    $scope.lastLogin = '';
    $scope.showCurrentPassword = false;
    $scope.showNewPassword = false;
    $scope.showConfirmPassword = false;

    $scope.init = function () {
        // Get user data from session
        $scope.user = window.currentUser || {};
        if (!$scope.user.id) {
            $http.get(window.baseUrl + '/API/auth/user')
                .then(function (response) {
                    $scope.user = response.data.user;
                    $scope.formatSLPhone();
                    $scope.lastLogin = $scope.user.last_login
                    loadProfileData();
                })
                .catch(function (error) {
                    console.error('Error loading user data:', error);
                });
        } else {
            loadProfileData();
        }
    };


    $scope.formatSLPhone = function () {
        if (!$scope.user.phone) return;

        // Remove non-digits
        let number = $scope.user.phone.toString().replace(/\D/g, '');

        // Remove leading 0 if length is 10
        if (number.length === 10 && number.startsWith('0')) {
            number = number.slice(1);
        }

        // Apply format if length is 9
        if (number.length === 9) {
            $scope.user.phone = '+94 ' + number.slice(0, 3) + ' ' + number.slice(3, 6) + ' ' + number.slice(6);
        } else {
            $scope.user.phone = number;
        }
    };

    function loadProfileData() {
        $scope.profileData = {
            name: $scope.user.name,
            email: $scope.user.email,
            phone: $scope.user.phone,
            reg_no: $scope.user.reg_no || '',
            username: $scope.user.username,
            note: $scope.user.note || ''
        };
    }

    $scope.getRoleName = function (roleId) {
        const roles = {
            1: 'Technical Support',
            2: 'Super Admin',
            3: 'Admin',
            4: 'Head of Department',
            5: 'Lecturer',
            6: 'Student',
            7: 'Parent'
        };
        return roles[roleId] || 'User';
    };

    // $scope.toggleEdit = function () {
    //     $scope.editing = !$scope.editing;
    //     $scope.errors = {};
    //     if (!$scope.editing) {
    //         loadProfileData(); // Reload original data
    //     }
    // };

    // $scope.validateProfile = function () {
    //     $scope.errors = {};
    //     let isValid = true;

    //     // Validate name
    //     if (!$scope.profileData.name || $scope.profileData.name.trim().length < 2) {
    //         $scope.errors.name = 'Name must be at least 2 characters long';
    //         isValid = false;
    //     }

    //     // Validate email
    //     const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    //     if (!$scope.profileData.email || !emailRegex.test($scope.profileData.email)) {
    //         $scope.errors.email = 'Please enter a valid email address';
    //         isValid = false;
    //     }

    //     // Validate phone
    //     if (!$scope.profileData.phone) {
    //         $scope.errors.phone = 'Phone number is required';
    //         isValid = false;
    //     } else if (!/^\d{9,15}$/.test($scope.profileData.phone.toString())) {
    //         $scope.errors.phone = 'Please enter a valid phone number (9-15 digits)';
    //         isValid = false;
    //     }

    //     // Validate username (for students)
    //     if ($scope.user.user_group === 6 && (!$scope.profileData.username || $scope.profileData.username.trim().length < 3)) {
    //         $scope.errors.username = 'Username must be at least 3 characters long';
    //         isValid = false;
    //     }

    //     return isValid;
    // };

    // $scope.saveProfile = function () {
    //     if (!$scope.validateProfile()) {
    //         return;
    //     }

    //     $scope.saving = true;
    //     $scope.errors = {};

    //     $http.put(window.baseUrl + '/API/profile/update', $scope.profileData)
    //         .then(function (response) {
    //             if (response.data.success) {
    //                 // Update session data
    //                 $scope.user.name = $scope.profileData.name;
    //                 $scope.user.email = $scope.profileData.email;
    //                 $scope.user.phone = $scope.profileData.phone;
    //                 $scope.user.reg_no = $scope.profileData.reg_no;
    //                 $scope.user.username = $scope.profileData.username;
    //                 $scope.user.note = $scope.profileData.note;

    //                 if (window.currentUser) {
    //                     window.currentUser.name = $scope.profileData.name;
    //                     window.currentUser.email = $scope.profileData.email;
    //                 }

    //                 $scope.editing = false;
    //                 showNotification('Profile updated successfully!', 'success');
    //             } else {
    //                 if (response.data.errors) {
    //                     $scope.errors = response.data.errors;
    //                 } else {
    //                     showNotification(response.data.message || 'Failed to update profile', 'error');
    //                 }
    //             }
    //         })
    //         .catch(function (error) {
    //             console.error('Error updating profile:', error);
    //             if (error.data && error.data.errors) {
    //                 $scope.errors = error.data.errors;
    //             } else {
    //                 showNotification('Error updating profile. Please try again.', 'error');
    //             }
    //         })
    //         .finally(function () {
    //             $scope.saving = false;
    //         });
    // };

    // Watch password for strength calculation
    $scope.$watch('passwordData.new_password', function (newPassword) {
        if (!newPassword) {
            $scope.passwordStrength = 0;
            $scope.passwordRequirements = {
                length: false,
                uppercase: false,
                lowercase: false,
                number: false
            };
            return;
        }

        // Check requirements
        const requirements = {
            length: newPassword.length >= 8,
            uppercase: /[A-Z]/.test(newPassword),
            lowercase: /[a-z]/.test(newPassword),
            number: /[0-9]/.test(newPassword)
        };

        $scope.passwordRequirements = requirements;

        // Calculate strength (0-3)
        let strength = 0;
        Object.values(requirements).forEach(met => {
            if (met) strength++;
        });

        $scope.passwordStrength = strength;
    });

    // Check if passwords match
    $scope.passwordsMatch = function () {
        return (
            $scope.passwordData.confirm_password &&
            $scope.passwordData.new_password === $scope.passwordData.confirm_password
        );
    };


    // Check if password form is valid
    $scope.isPasswordFormValid = function () {
        return !!(
            $scope.passwordData.current_password &&
            $scope.passwordData.new_password &&
            $scope.passwordData.confirm_password &&
            $scope.passwordsMatch() &&
            $scope.passwordStrength >= 3
        );
    };


    $scope.changePassword = function () {
        if (!$scope.isPasswordFormValid()) {
            if (!$scope.passwordData.current_password) {
                showNotification('Please enter your current password', 'error');
            } else if (!$scope.passwordsMatch()) {
                showNotification('New passwords do not match', 'error');
            } else if ($scope.passwordStrength < 3) {
                showNotification('Password does not meet security requirements', 'error');
            }
            return;
        }

        $scope.changingPassword = true;

        $http.post(window.baseUrl + '/API/profile/change-password', {
            current_password: $scope.passwordData.current_password,
            new_password: $scope.passwordData.new_password
        })
            .then(function (response) {
                if (response.data.status === 'success') {
                    showNotification('Password changed successfully!', 'success');
                    // Clear form
                    $scope.passwordData = {
                        current_password: '',
                        new_password: '',
                        confirm_password: ''
                    };
                    $scope.passwordStrength = 0;
                } else {
                    showNotification(response.data.msg || 'Failed to change password', 'error');
                }
            })
            .catch(function (error) {
                console.error('Error changing password:', error);
                if (error.status === 401) {
                    showNotification('Current password is incorrect', 'error');
                } else {
                    showNotification('Error changing password', 'error');
                }
            })
            .finally(function () {
                $scope.changingPassword = false;
            });
    };

    // $scope.uploadAvatar = function (event) {
    //     const file = event.target.files[0];
    //     if (!file) return;

    //     const formData = new FormData();
    //     formData.append('avatar', file);

    //     $http.post(window.baseUrl + '/API/profile/upload-avatar', formData, {
    //         headers: { 'Content-Type': undefined },
    //         transformRequest: angular.identity
    //     })
    //         .then(function (response) {
    //             if (response.data.success) {
    //                 showNotification('Profile picture updated', 'success');
    //             } else {
    //                 showNotification(response.data.message || 'Failed to upload image', 'error');
    //             }
    //         })
    //         .catch(function (error) {
    //             console.error('Error uploading avatar:', error);
    //             showNotification('Error uploading profile picture', 'error');
    //         });
    // };

    // $scope.logoutSession = function (sessionId) {
    //     if (!confirm('Are you sure you want to logout from this device?')) {
    //         return;
    //     }

    //     $http.delete(`<?php echo BASE_URL ?>/api/profile/logout-session/${sessionId}`)
    //         .then(function (response) {
    //             if (response.data.success) {
    //                 $scope.activeSessions = $scope.activeSessions.filter(session => session.id !== sessionId);
    //                 showNotification('Logged out from device', 'success');
    //             } else {
    //                 showNotification('Failed to logout from device', 'error');
    //             }
    //         })
    //         .catch(function (error) {
    //             console.error('Error logging out session:', error);
    //             showNotification('Error logging out from device', 'error');
    //         });
    // };

    // $scope.logoutAllOtherSessions = function () {
    //     if (!confirm('Are you sure you want to logout from all other devices?')) {
    //         return;
    //     }

    //     $http.post(window.baseUrl + '/API/profile/logout-all-sessions')
    //         .then(function (response) {
    //             if (response.data.success) {
    //                 $scope.activeSessions = $scope.activeSessions.filter(session => session.is_current);
    //                 showNotification('Logged out from all other devices', 'success');
    //             } else {
    //                 showNotification('Failed to logout from other devices', 'error');
    //             }
    //         })
    //         .catch(function (error) {
    //             console.error('Error logging out all sessions:', error);
    //             showNotification('Error logging out from other devices', 'error');
    //         });
    // };

    // $scope.exportData = function () {
    //     $http.get(window.baseUrl + '/API/profile/export-data', { responseType: 'blob' })
    //         .then(function (response) {
    //             const url = window.URL.createObjectURL(response.data);
    //             const a = document.createElement('a');
    //             a.href = url;
    //             a.download = `user-data-${$scope.user.id}-${new Date().toISOString().split('T')[0]}.json`;
    //             document.body.appendChild(a);
    //             a.click();
    //             window.URL.revokeObjectURL(url);
    //             document.body.removeChild(a);

    //             showNotification('Data export started. Check your downloads.', 'success');
    //         })
    //         .catch(function (error) {
    //             console.error('Error exporting data:', error);
    //             showNotification('Error exporting data', 'error');
    //         });
    // };

    $scope.exportData = function () {
        let format = null;

        Toast.popover({
            type: 'confirm',
            title: 'Export Data',
            titleColor: '#fdfdfd',
            content: '<i class="fas fa-download text-5xl"></i><br><br>Click button to export your data as <b>JSON</b> or <b>PDF</b>.',
            contentColor: '#fdfdfd',
            options: {
                confirm: {
                    text: '<i class="fas fa-file-pdf mr-1"></i>Export as PDF',
                    background: '#0891b2', // cyan-600
                    onConfirm: function () { proceedExport('pdf'); }
                },
                cancel: {
                    text: '<i class="fas fa-file-code mr-1"></i>Export as JSON',
                    background: '#16a34a', // green-600
                    onCancel: function () { proceedExport('json'); }
                }
            }
        });

        function proceedExport(format) {
            $http.get(window.baseUrl + '/API/profile/export-data?format=' + format.toLowerCase(), { responseType: 'blob' })
                .then(function (response) {
                    const url = window.URL.createObjectURL(response.data);
                    const a = document.createElement('a');
                    a.href = url;

                    // Set file extension based on format
                    const ext = format.toLowerCase() === 'json' ? 'json' : 'pdf';
                    a.download = `user-data-${$scope.user.id}-${new Date().toISOString().split('T')[0]}.${ext}`;

                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);

                    showNotification(`Data export started as ${ext.toUpperCase()}. Check your downloads.`, 'success');
                })
                .catch(function (error) {
                    console.error('Error exporting data:', error);
                    showNotification('Error exporting data', 'error');
                });
            Toast.popover({ type: 'close' })
        }
    };

    $scope.deleteAccount = function () {
        Toast.popover({
            type: 'content',
            title: 'Delete Account',
            titleColor: '#dc2626',
            content: `
                    <div style="text-align: center;">
                    <i class="fas fa-exclamation-triangle" 
                       style="font-size: 3rem; color: #dc2626; margin-bottom: 1rem;"></i>
                    <p style="margin-bottom: 0.5rem;">This action cannot be undone. All your data will be permanently deleted.</p>
                    <p style="margin-bottom: 1rem;">Type <b>DELETE</b> below to confirm:</p>
                    <input type="text" id="deleteInput" placeholder="Type DELETE"
                           style="border-radius: 0.25rem; padding: 0.25rem 0.5rem; width: 100%; text-align: center; background: transparent;"/>
                </div>
            `,
            buttons: [
                {
                    text: '<i class="fas fa-trash mr-1"></i>Delete Account',
                    background: '#dc2626',
                    color: '#fff',
                    onClick: async (popover) => {
                        const input = document.getElementById('deleteInput');
                        if (!input || input.value !== 'DELETE') {
                            showNotification('You must type DELETE to confirm.', 'warning');
                            return;
                        }
                        await $http({
                            url: window.baseUrl + '/API/profile/delete-account',
                            method: 'DELETE'
                        }).then(function (response) {
                            if (response.data.status === 'success') {
                                showNotification('Account deletion requested. Logging out...', 'success');
                                setTimeout(() => {
                                    window.location.href = window.baseUrl + '/login';
                                }, 2000);
                            } else {
                                showNotification(result.message || 'Failed to delete account', 'error');
                            }
                        })
                    }
                },
                {
                    text: '<i class="fas fa-times mr-1"></i>Cancel',
                    background: '#6b7280',
                    color: '#fff',
                    onClick: () => {
                        Toast.popover({ type: 'close' })
                    }
                }
            ],
            size: 'sm',
            buttonPosition: 'center',
            position: 'center'
        });
    };

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-[9999999] px-6 py-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full max-w-sm`;

        const typeClasses = {
            success: 'bg-green-600 text-white border-l-4 border-green-700',
            error: 'bg-red-600 text-white border-l-4 border-red-700',
            info: 'bg-blue-600 text-white border-l-4 border-blue-700'
        };

        notification.className += ' ' + (typeClasses[type] || typeClasses.info);
        notification.innerHTML = `
            <div class="flex items-center space-x-3">
                <i class="fas text-lg ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-times-circle' : 'fa-info-circle'}"></i>
                <span class="flex-1">${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(notification);

        $timeout(() => {
            notification.classList.remove('translate-x-full');
            notification.classList.add('translate-x-0');
        }, 10);

        $timeout(() => {
            if (notification.parentElement) {
                notification.classList.remove('translate-x-0');
                notification.classList.add('translate-x-full');
                $timeout(() => {
                    if (notification.parentElement) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }
        }, 5000);
    }
}]);