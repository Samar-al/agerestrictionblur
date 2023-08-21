/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */
document.addEventListener('DOMContentLoaded', function () {
  let ageRestrictionContainer = document.querySelector('.page-container');
  let yesButton = document.getElementById('age-restriction-yes');
  let noButton = document.getElementById('age-restriction-no');

  // Function to check if the user has already verified their age
  function isAgeRestricted() {
    return isAgeVerified() || isUnderage();
  }

  yesButton.addEventListener('click', function () {
    let cookieLifetime = yesButton.getAttribute('data-cookie-lifetime');
    setAgeVerifiedCookie(false, cookieLifetime);
  });

  noButton.addEventListener('click', function () {
    let cookieLifetime = noButton.getAttribute('data-cookie-lifetime');
    setAgeVerifiedCookie(true, cookieLifetime);

    //window.location.href = "http://www.google.fr";
    // Retrieve the URL from the AGE_RESTRICTION_BLUR_REDIRECTION configuration variable
    let redirectionURL = noButton.getAttribute('data-redirection-url');
    if (redirectionURL) {
      window.location.href = redirectionURL;
    }
  });

  ageRestrictionContainer.addEventListener('contextmenu', function (event) {
    event.preventDefault();
  
    // Show the "Not Authorized" message
    showNotAuthorizedMessage(event.clientX, event.clientY);
  });

  // Function to show the "Not Authorized" message
  function showNotAuthorizedMessage(x, y) {
    let notAuthorizedMessage = document.createElement('div');
    notAuthorizedMessage.textContent = 'Right-click is not authorized here.';
    notAuthorizedMessage.classList.add('not-authorized-message');
    notAuthorizedMessage.style.position = 'fixed';
    notAuthorizedMessage.style.top = y + 'px';
    notAuthorizedMessage.style.left = x + 'px';
    notAuthorizedMessage.style.zIndex = '99999';
    notAuthorizedMessage.style.padding = '10px';
    notAuthorizedMessage.style.backgroundColor = '#1b7a7d'; // background color
    notAuthorizedMessage.style.color = '#fff'; // White text color
    notAuthorizedMessage.style.fontWeight = 'bold';
    notAuthorizedMessage.style.borderRadius = '5px';
    notAuthorizedMessage.style.boxShadow = '0 0 10px rgba(0, 0, 0, 0.3)'; // Add a shadow
    document.body.appendChild(notAuthorizedMessage);

    // Hide the message after a few seconds (adjust the time delay as needed)
    setTimeout(function () {
      document.body.removeChild(notAuthorizedMessage);
    }, 2000); // 2 seconds
  }

  if (isAgeVerified() && !isUnderage()) {
    // If the user has already verified their age and is not underage, hide the age verification pop-up
    ageRestrictionContainer.style.display = 'none';
  } else {
    // For all other cases, show the age verification pop-up
    ageRestrictionContainer.style.display = 'flex';
  }
  
  function setAgeVerifiedCookie(isUnderage, cookieLifetime) {
    // Set a cookie to remember the user's age verification status
    let expirationDate = new Date();
    expirationDate.setDate(expirationDate.getDate() + parseInt(cookieLifetime));
    document.cookie = 'ageVerifiedBlur=true; expires=' + expirationDate.toUTCString() + '; path=/';
    document.cookie = 'underageBlur=' + (isUnderage ? 'true' : 'false') + '; expires=' + expirationDate.toUTCString() + '; path=/';
    
    // Hide the age verification pop-up
    ageRestrictionContainer.style.display = 'none';
  }

  function isAgeVerified() {
    return document.cookie.indexOf('ageVerifiedBlur=true') !== -1;
  }

  function isUnderage() {
    const cookies = document.cookie.split('; ');
    for (const cookie of cookies) {
      const [name, value] = cookie.split('=');
      if (name === 'underageBlur' && value === 'true') {
        return true;
      }
    }
    return false;
  }
});
  