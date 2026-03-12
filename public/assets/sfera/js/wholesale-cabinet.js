// ===== Sidebar toggle =====
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const mainWrap = document.getElementById('mainWrap');
  sidebar.classList.toggle('collapsed');
  mainWrap.classList.toggle('expanded');
}

function openMobile() {
  document.getElementById('sidebar').classList.add('mobile-open');
  document.getElementById('mobileOverlay').classList.add('visible');
}

function closeMobile() {
  document.getElementById('sidebar').classList.remove('mobile-open');
  document.getElementById('mobileOverlay').classList.remove('visible');
}

// ===== Shared SVGs =====
const usersSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>';

const shareSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" x2="15.42" y1="13.51" y2="17.49"/><line x1="15.41" x2="8.59" y1="6.51" y2="10.49"/></svg>';

const moreSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>';

// ===== Shared Data =====
const apps = [
  { name: "PixelMaster", iconClass: "ic-violet", iconSvg: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>', description: "Advanced image editing and composition", category: "Creative", recent: true, isNew: false, progress: 100 },
  { name: "VectorPro", iconClass: "ic-orange", iconSvg: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9.06 11.9 8.07-8.06a2.85 2.85 0 1 1 4.03 4.03l-8.06 8.08"/><path d="M7.07 14.94c-1.66 0-3 1.35-3 3.02 0 1.33-2.5 1.52-2 2.02 1.08 1.1 2.49 2.02 4 2.02 2.2 0 4-1.8 4-4.04a3.01 3.01 0 0 0-3-3.02z"/></svg>', description: "Professional vector graphics creation", category: "Creative", recent: true, isNew: false, progress: 100 },
  { name: "VideoStudio", iconClass: "ic-pink", iconSvg: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 13 5.223 3.482a.5.5 0 0 0 .777-.416V7.87a.5.5 0 0 0-.752-.432L16 10.5"/><rect x="2" y="6" width="14" height="12" rx="2"/></svg>', description: "Cinematic video editing and production", category: "Video", recent: true, isNew: false, progress: 100 },
  { name: "MotionFX", iconClass: "ic-blue", iconSvg: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/></svg>', description: "Stunning visual effects and animations", category: "Video", recent: false, isNew: false, progress: 100 },
  { name: "PageCraft", iconClass: "ic-red", iconSvg: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z"/><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"/><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"/></svg>', description: "Professional page design and layout", category: "Creative", recent: false, isNew: false, progress: 100 },
  { name: "UXFlow", iconClass: "ic-fuchsia", iconSvg: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>', description: "Intuitive user experience design", category: "Design", recent: false, isNew: true, progress: 85 },
  { name: "PhotoLab", iconClass: "ic-teal", iconSvg: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>', description: "Advanced photo editing and organization", category: "Photography", recent: false, isNew: false, progress: 100 },
  { name: "DocMaster", iconClass: "ic-red-dark", iconSvg: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>', description: "Document editing and management", category: "Document", recent: false, isNew: false, progress: 100 },
  { name: "WebCanvas", iconClass: "ic-emerald", iconSvg: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>', description: "Web design and development", category: "Web", recent: false, isNew: true, progress: 70 },
  { name: "3DStudio", iconClass: "ic-indigo", iconSvg: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21 16-4 4-4-4"/><path d="M17 20V4"/><path d="m3 8 4-4 4 4"/><path d="M7 4v16"/></svg>', description: "3D modeling and rendering", category: "3D", recent: false, isNew: true, progress: 60 },
  { name: "FontForge", iconClass: "ic-amber", iconSvg: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="4 7 4 4 20 4 20 7"/><line x1="9" x2="15" y1="20" y2="20"/><line x1="12" x2="12" y1="4" y2="20"/></svg>', description: "Typography and font creation", category: "Typography", recent: false, isNew: false, progress: 100 },
  { name: "ColorPalette", iconClass: "ic-purple", iconSvg: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/><circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/><circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/><circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"/></svg>', description: "Color scheme creation and management", category: "Design", recent: false, isNew: false, progress: 100 },
];

const recentFiles = [
  { name: "Brand Redesign.pxm", app: "PixelMaster", modified: "2 hours ago", iconClass: "ic-violet", iconSvg: apps[0].iconSvg, shared: true, size: "24.5 MB", collaborators: 3 },
  { name: "Company Logo.vec", app: "VectorPro", modified: "Yesterday", iconClass: "ic-orange", iconSvg: apps[1].iconSvg, shared: true, size: "8.2 MB", collaborators: 2 },
  { name: "Product Launch Video.vid", app: "VideoStudio", modified: "3 days ago", iconClass: "ic-pink", iconSvg: apps[2].iconSvg, shared: false, size: "1.2 GB", collaborators: 0 },
  { name: "UI Animation.mfx", app: "MotionFX", modified: "Last week", iconClass: "ic-blue", iconSvg: apps[3].iconSvg, shared: true, size: "345 MB", collaborators: 4 },
  { name: "Magazine Layout.pgc", app: "PageCraft", modified: "2 weeks ago", iconClass: "ic-red", iconSvg: apps[4].iconSvg, shared: false, size: "42.8 MB", collaborators: 0 },
  { name: "Mobile App Design.uxf", app: "UXFlow", modified: "3 weeks ago", iconClass: "ic-fuchsia", iconSvg: apps[5].iconSvg, shared: true, size: "18.3 MB", collaborators: 5 },
  { name: "Product Photography.phl", app: "PhotoLab", modified: "Last month", iconClass: "ic-teal", iconSvg: apps[6].iconSvg, shared: false, size: "156 MB", collaborators: 0 },
];

const projects = [
  { name: "Website Redesign", description: "Complete overhaul of company website", progress: 75, dueDate: "June 15, 2025", members: 4, files: 23 },
  { name: "Mobile App Launch", description: "Design and assets for new mobile application", progress: 60, dueDate: "July 30, 2025", members: 6, files: 42 },
  { name: "Brand Identity", description: "New brand guidelines and assets", progress: 90, dueDate: "May 25, 2025", members: 3, files: 18 },
  { name: "Marketing Campaign", description: "Summer promotion materials", progress: 40, dueDate: "August 10, 2025", members: 5, files: 31 },
];

const tutorials = [
  { title: "Mastering Digital Illustration", description: "Learn advanced techniques for creating stunning digital art", duration: "1h 45m", level: "Advanced", instructor: "Sarah Chen", category: "Illustration", views: "24K" },
  { title: "UI/UX Design Fundamentals", description: "Essential principles for creating intuitive user interfaces", duration: "2h 20m", level: "Intermediate", instructor: "Michael Rodriguez", category: "Design", views: "56K" },
  { title: "Video Editing Masterclass", description: "Professional techniques for cinematic video editing", duration: "3h 10m", level: "Advanced", instructor: "James Wilson", category: "Video", views: "32K" },
  { title: "Typography Essentials", description: "Create beautiful and effective typography for any project", duration: "1h 30m", level: "Beginner", instructor: "Emma Thompson", category: "Typography", views: "18K" },
  { title: "Color Theory for Designers", description: "Understanding color relationships and psychology", duration: "2h 05m", level: "Intermediate", instructor: "David Kim", category: "Design", views: "41K" },
];

const communityPosts = [
  { title: "Minimalist Logo Design", author: "Alex Morgan", likes: 342, comments: 28, time: "2 days ago" },
  { title: "3D Character Concept", author: "Priya Sharma", likes: 518, comments: 47, time: "1 week ago" },
  { title: "UI Dashboard Redesign", author: "Thomas Wright", likes: 276, comments: 32, time: "3 days ago" },
  { title: "Product Photography Setup", author: "Olivia Chen", likes: 189, comments: 15, time: "5 days ago" },
];

// ===== App Card Helper =====
function appCard(app, showNew) {
  return `<div class="card">
    <div class="card-header">
      <div class="card-header-row">
        <div class="card-icon"><span class="${app.iconClass}">${app.iconSvg}</span></div>
        ${showNew && app.isNew ? '<span class="badge badge-amber">New</span>' : ''}
        ${!showNew ? `<button class="btn btn-ghost btn-icon-sm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></button>` : ''}
      </div>
    </div>
    <div class="card-body">
      <h3>${app.name}</h3>
      <p>${app.description}</p>
      ${showNew && app.isNew && app.progress < 100 ? `
        <div class="progress-wrap" style="margin-top:8px">
          <div class="progress-header"><span>Installation</span><span>${app.progress}%</span></div>
          <div class="progress-bar"><div class="progress-fill" style="width:${app.progress}%"></div></div>
        </div>` : ''}
    </div>
    <div class="card-footer">
      <div class="card-footer-row">
        <button class="btn btn-secondary btn-block">${app.progress < 100 ? (showNew ? 'Continue Install' : 'Install') : 'Open'}</button>
        ${!showNew ? `<button class="btn btn-outline btn-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></button>` : ''}
      </div>
    </div>
  </div>`;
}

// ===== User Menu Dropdown =====
document.addEventListener('DOMContentLoaded', function() {
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userMenu = document.getElementById('userMenu');
    
    if (userMenuBtn && userMenu) {
        // Toggle menu on button click
        userMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userMenu.classList.toggle('show');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!userMenu.contains(e.target) && e.target !== userMenuBtn) {
                userMenu.classList.remove('show');
            }
        });
        
        // Prevent menu from closing when clicking inside
        userMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});
