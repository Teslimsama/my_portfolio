import React, { useState, useEffect } from 'react';
import { EXPERIENCE_DATA, PROJECTS_DATA, SKILLS_DATA } from './constants';

type ViewState = 'home' | 'services';

const Navbar = ({ setView, currentView }: { setView: (v: ViewState) => void, currentView: ViewState }) => {
  const handleNav = (id: string) => {
    if (currentView !== 'home') {
      setView('home');
      setTimeout(() => {
        const el = document.getElementById(id);
        if (el) el.scrollIntoView({ behavior: 'smooth' });
      }, 100);
    } else {
      const el = document.getElementById(id);
      if (el) el.scrollIntoView({ behavior: 'smooth' });
    }
  };

  return (
    <nav className="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between h-16 items-center">
          <div 
            className="flex items-center space-x-2 cursor-pointer" 
            onClick={() => { setView('home'); window.scrollTo({ top: 0, behavior: 'smooth' }); }}
          >
            <span className="text-xl font-bold text-slate-900 tracking-tight">Teslim Bolaji</span>
          </div>
          <div className="hidden md:flex space-x-8 text-sm font-medium text-slate-600 items-center">
            <button 
              onClick={() => { setView('home'); window.scrollTo({ top: 0, behavior: 'smooth' }); }} 
              className={`hover:text-blue-600 transition-colors ${currentView === 'home' ? 'text-blue-600' : ''}`}
            >
              Home
            </button>
            <button 
              onClick={() => { setView('services'); window.scrollTo({ top: 0, behavior: 'smooth' }); }} 
              className={`hover:text-blue-600 transition-colors ${currentView === 'services' ? 'text-blue-600' : ''}`}
            >
              Detailed Services
            </button>
            <button onClick={() => handleNav('experience')} className="hover:text-blue-600 transition-colors">Experience</button>
            <button onClick={() => handleNav('projects')} className="hover:text-blue-600 transition-colors">Projects</button>
            <button 
              onClick={() => {
                const el = document.getElementById('contact');
                if (el) el.scrollIntoView({ behavior: 'smooth' });
              }} 
              className="px-4 py-2 bg-slate-900 text-white rounded-md hover:bg-slate-800 transition-all"
            >
              Hire Me
            </button>
          </div>
        </div>
      </div>
    </nav>
  );
};

const Hero = ({ setView }: { setView: (v: ViewState) => void }) => (
  <section className="py-12 md:py-24 bg-gradient-to-b from-slate-50 to-white overflow-hidden">
    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div className="flex flex-col-reverse lg:flex-row items-center gap-12 lg:gap-16">
        {/* Left Content */}
        <div className="flex-1 text-center lg:text-left">
          <div className="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100 mb-6">
            Systems & Software Engineer
          </div>
          <h1 className="text-4xl md:text-5xl lg:text-7xl font-bold text-slate-900 leading-tight mb-6">
            Building & Scaling <span className="text-blue-600">Reliable</span> Systems.
          </h1>
          <p className="text-lg md:text-xl text-slate-600 leading-relaxed mb-8 max-w-2xl mx-auto lg:mx-0">
            I architect robust backend infrastructures and provide mission-critical support for enterprise applications, ensuring performance and stability.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
            <button 
              onClick={() => { setView('services'); window.scrollTo({ top: 0, behavior: 'smooth' }); }} 
              className="px-8 py-4 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200"
            >
              Explore Detailed Services
            </button>
            <button 
              onClick={() => {
                const el = document.getElementById('contact');
                if (el) el.scrollIntoView({ behavior: 'smooth' });
              }}
              className="px-8 py-4 bg-white border border-slate-200 text-slate-900 font-semibold rounded-xl hover:border-slate-300 hover:bg-slate-50 transition-all shadow-sm"
            >
              Contact Me
            </button>
          </div>
        </div>

        {/* Right Content - Portrait */}
        <div className="flex-1 relative w-full max-w-sm md:max-w-md lg:max-w-lg">
          <div className="relative z-10 group">
            {/* Background Decorative Accent */}
            <div className="absolute -inset-4 bg-blue-600/5 rounded-[2.5rem] -rotate-3 transition-transform group-hover:rotate-0 duration-500"></div>
            <div className="absolute -inset-4 border-2 border-slate-100 rounded-[2.5rem] rotate-2 transition-transform group-hover:rotate-0 duration-500"></div>
            
            {/* Main Portrait Frame */}
            <div className="relative overflow-hidden rounded-3xl shadow-2xl border-4 border-white bg-slate-100">
              <img 
                src="https://api.aistudio.google.com/v1/files/portrait_teslim.jpg" 
                alt="Teslim Bolaji" 
                className="w-full h-auto object-cover transform transition-transform duration-700 group-hover:scale-105"
                style={{ aspectRatio: '4/5' }}
                onError={(e) => {
                  // Fallback for demo purposes if the specific file path isn't mapped
                  (e.target as HTMLImageElement).src = "https://images.unsplash.com/photo-1531384441138-2736e62e0919?q=80&w=1000&auto=format&fit=crop";
                }}
              />
            </div>
            
            {/* Status Badge */}
            <div className="absolute -bottom-4 -right-4 bg-white p-4 rounded-2xl shadow-xl flex items-center space-x-3 border border-slate-100 animate-bounce-slow">
              <div className="relative">
                <div className="w-3 h-3 bg-green-500 rounded-full"></div>
                <div className="absolute inset-0 w-3 h-3 bg-green-500 rounded-full animate-ping opacity-75"></div>
              </div>
              <span className="text-xs font-bold text-slate-800 tracking-wide uppercase">Open for Hire</span>
            </div>
          </div>
          
          {/* Subtle Glow Behind Image */}
          <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[140%] h-[140%] bg-blue-400 opacity-[0.05] rounded-full blur-3xl -z-10"></div>
        </div>
      </div>
    </div>
  </section>
);

const SectionHeading = ({ title, subtitle, light = false }: { title: string; subtitle: string; light?: boolean }) => (
  <div className="mb-12">
    <h2 className={`text-3xl font-bold mb-2 ${light ? 'text-white' : 'text-slate-900'}`}>{title}</h2>
    <div className="w-12 h-1 bg-blue-600 mb-4"></div>
    <p className={light ? 'text-slate-400' : 'text-slate-600'}>{subtitle}</p>
  </div>
);

const HomeView = ({ setView }: { setView: (v: ViewState) => void }) => (
  <>
    <Hero setView={setView} />
    <section id="about" className="py-20 border-t border-slate-100 bg-white scroll-mt-20">
      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <div>
            <SectionHeading 
              title="About Me" 
              subtitle="Systems-focused engineer with a passion for stability." 
            />
            <div className="space-y-4 text-slate-600 leading-relaxed text-base">
              <p>
                With deep experience in backend architecture and application support, I bridge the gap between building high-performance features and maintaining production-grade reliability.
              </p>
              <p>
                I thrive in environments where system uptime and data integrity are paramount. My background includes working with unfamiliar codebases, refactoring legacy systems, and scaling multi-tenant platforms.
              </p>
            </div>
          </div>
          <div className="grid grid-cols-2 gap-4">
            {[
              { label: 'Uptime Focused', desc: 'SLA-driven development' },
              { label: 'Fullstack Capable', desc: 'PHP/Laravel & JS' },
              { label: 'Integration Expert', desc: 'Payments & Messaging' },
              { label: 'Support Driven', desc: 'Enterprise debugging' },
            ].map((item, i) => (
              <div key={i} className="p-6 bg-slate-50 rounded-xl border border-slate-200 group hover:border-blue-300 hover:bg-white transition-all">
                <h4 className="font-bold text-slate-900 mb-1 text-sm md:text-base group-hover:text-blue-600">{item.label}</h4>
                <p className="text-xs md:text-sm text-slate-500">{item.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>

    <section id="services-preview" className="py-20 bg-slate-900 text-white scroll-mt-20">
      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="mb-16 text-center">
          <h2 className="text-3xl font-bold mb-4">Core Competencies</h2>
          <p className="text-slate-400 max-w-2xl mx-auto">Providing end-to-end technical solutions from architecture to support.</p>
        </div>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
          {[
            { title: 'Backend & API', desc: 'Scalable microservices using PHP/Laravel with a focus on REST principles.' },
            { title: 'Tech Support', desc: 'Incident resolution and maintaining stability under strict SLAs.' },
            { title: 'Integrations', desc: 'Seamlessly connecting platforms with third-party payment and messaging APIs.' },
          ].map((s, i) => (
            <div key={i} className="p-8 bg-slate-800/50 rounded-2xl border border-slate-700 hover:border-blue-500 transition-colors">
              <h3 className="text-xl font-bold mb-3">{s.title}</h3>
              <p className="text-slate-400 text-sm leading-relaxed">{s.desc}</p>
            </div>
          ))}
        </div>
        <div className="text-center">
          <button onClick={() => { setView('services'); window.scrollTo({ top: 0, behavior: 'smooth' }); }} className="text-blue-400 font-semibold hover:text-blue-300 flex items-center justify-center mx-auto group">
            See All Detailed Services 
            <svg className="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
          </button>
        </div>
      </div>
    </section>
  </>
);

const ServicesPage = ({ setView }: { setView: (v: ViewState) => void }) => (
  <div className="bg-white min-h-screen">
    <div className="bg-slate-50 border-b border-slate-200 py-16">
      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <button 
          onClick={() => { setView('home'); window.scrollTo({ top: 0, behavior: 'smooth' }); }} 
          className="flex items-center text-sm font-medium text-slate-500 hover:text-blue-600 mb-8 transition-colors group"
        >
          <svg className="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          Back to Portfolio
        </button>
        <h1 className="text-4xl font-bold text-slate-900 mb-4">Detailed Service Offerings</h1>
        <p className="text-xl text-slate-600 max-w-3xl leading-relaxed">
          I provide professional-grade engineering and support services tailored for enterprise systems, multi-tenant platforms, and high-growth technology businesses.
        </p>
      </div>
    </div>

    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
      <div className="space-y-24">
        {/* Service 1 */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-12">
          <div className="lg:col-span-1">
            <div className="sticky top-24">
              <span className="text-blue-600 font-bold text-sm tracking-widest uppercase">01</span>
              <h2 className="text-2xl font-bold text-slate-900 mt-2 mb-4">Backend & API Architecture</h2>
              <p className="text-slate-600 text-sm leading-relaxed">
                Building the "brain" of your application with a focus on longevity and performance.
              </p>
            </div>
          </div>
          <div className="lg:col-span-2 space-y-6">
            <div className="bg-slate-50 p-8 rounded-2xl border border-slate-200">
              <h3 className="font-bold text-slate-900 mb-4">Approach & Methodology</h3>
              <ul className="space-y-4 text-slate-600">
                <li className="flex gap-4">
                  <div className="w-6 h-6 rounded-full bg-blue-100 flex-shrink-0 flex items-center justify-center text-blue-600 font-bold text-xs">A</div>
                  <div><strong>RESTful Principles:</strong> I design APIs that follow strict REST patterns, ensuring they are intuitive for other developers to consume and integrate.</div>
                </li>
                <li className="flex gap-4">
                  <div className="w-6 h-6 rounded-full bg-blue-100 flex-shrink-0 flex items-center justify-center text-blue-600 font-bold text-xs">B</div>
                  <div><strong>Security-First:</strong> Implementing OAuth2, JWT, and rigorous request validation to protect sensitive enterprise data.</div>
                </li>
                <li className="flex gap-4">
                  <div className="w-6 h-6 rounded-full bg-blue-100 flex-shrink-0 flex items-center justify-center text-blue-600 font-bold text-xs">C</div>
                  <div><strong>Performance:</strong> Leveraging caching strategies with Redis and optimizing background processes for heavy computation.</div>
                </li>
              </ul>
            </div>
          </div>
        </div>

        {/* Service 2 */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-12">
          <div className="lg:col-span-1">
            <div className="sticky top-24">
              <span className="text-blue-600 font-bold text-sm tracking-widest uppercase">02</span>
              <h2 className="text-2xl font-bold text-slate-900 mt-2 mb-4">Technical & Platform Support</h2>
              <p className="text-slate-600 text-sm leading-relaxed">
                Maintaining the health and reliability of production systems through rigorous oversight.
              </p>
            </div>
          </div>
          <div className="lg:col-span-2 space-y-6">
            <div className="bg-slate-900 p-8 rounded-2xl text-white shadow-xl">
              <h3 className="font-bold mb-4 text-blue-400 uppercase tracking-wide text-sm">Support Excellence</h3>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                  <h4 className="font-bold mb-2">SLA Management</h4>
                  <p className="text-slate-400 text-sm">I am used to working within strict Response and Resolution timeframes for enterprise clients.</p>
                </div>
                <div>
                  <h4 className="font-bold mb-2">Log Analysis</h4>
                  <p className="text-slate-400 text-sm">Deep-diving into system logs to identify root causes and prevent recurrence of critical bugs.</p>
                </div>
                <div>
                  <h4 className="font-bold mb-2">Incident Resolution</h4>
                  <p className="text-slate-400 text-sm">Rapid response to platform outages, handling stress with a focus on stabilization.</p>
                </div>
                <div>
                  <h4 className="font-bold mb-2">Documentation</h4>
                  <p className="text-slate-400 text-sm">Ensuring every fix is documented in internal KBs to speed up future support tasks.</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Service 3 */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-12">
          <div className="lg:col-span-1">
            <div className="sticky top-24">
              <span className="text-blue-600 font-bold text-sm tracking-widest uppercase">03</span>
              <h2 className="text-2xl font-bold text-slate-900 mt-2 mb-4">Systems Integration</h2>
              <p className="text-slate-600 text-sm leading-relaxed">
                Connecting your business logic to the global digital ecosystem.
              </p>
            </div>
          </div>
          <div className="lg:col-span-2">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="p-6 border border-slate-200 rounded-xl hover:border-blue-400 transition-colors">
                <h4 className="font-bold text-slate-900 mb-2">Payment Gateways</h4>
                <p className="text-slate-600 text-sm">Integration of systems like Paystack and various VTU APIs for seamless automated billing and collection.</p>
              </div>
              <div className="p-6 border border-slate-200 rounded-xl hover:border-blue-400 transition-colors">
                <h4 className="font-bold text-slate-900 mb-2">Messaging APIs</h4>
                <p className="text-slate-600 text-sm">Implementing reliable SMS and notification pipelines using providers like Termii and SendGrid.</p>
              </div>
              <div className="p-6 border border-slate-200 rounded-xl hover:border-blue-400 transition-colors">
                <h4 className="font-bold text-slate-900 mb-2">ERP/LMS Connectors</h4>
                <p className="text-slate-600 text-sm">Linking custom software with existing enterprise resource planning or learning management systems.</p>
              </div>
              <div className="p-6 border border-slate-200 rounded-xl hover:border-blue-400 transition-colors">
                <h4 className="font-bold text-slate-900 mb-2">Background Sync</h4>
                <p className="text-slate-600 text-sm">Developing robust webhook handlers and background jobs to keep data synchronized across platforms.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div className="mt-24 p-12 bg-blue-50 border border-blue-100 rounded-3xl text-center">
        <h3 className="text-2xl font-bold text-slate-900 mb-4">Need a Detailed Proposal?</h3>
        <p className="text-slate-600 mb-8 max-w-xl mx-auto">
          If you have a specific project or system that needs architectural review or dedicated support, let's discuss the technical requirements.
        </p>
        <button 
          onClick={() => {
            const el = document.getElementById('contact');
            if (el) el.scrollIntoView({ behavior: 'smooth' });
          }}
          className="inline-block px-8 py-4 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition-all"
        >
          Contact Me for a Consultation
        </button>
      </div>
    </div>
  </div>
);

const Experience = () => (
  <section id="experience" className="py-20 border-t border-slate-100 bg-white scroll-mt-20">
    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <SectionHeading 
        title="Professional Experience" 
        subtitle="A track record of building and supporting enterprise systems." 
      />
      <div className="space-y-8">
        {EXPERIENCE_DATA.map((exp) => (
          <div key={exp.id} className="relative pl-8 pb-8 border-l border-slate-200 last:pb-0">
            <div className="absolute -left-1.5 top-0 w-3 h-3 rounded-full bg-blue-600"></div>
            <div className="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
              <div>
                <h3 className="text-xl font-bold text-slate-900">{exp.role}</h3>
                <div className="flex items-center space-x-2 text-slate-500 font-medium text-sm">
                  <span>{exp.company}</span>
                  <span>•</span>
                  <span>{exp.type}</span>
                </div>
              </div>
              <div className="text-xs font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded mt-2 md:mt-0 self-start md:self-center">
                {exp.period}
              </div>
            </div>
            <ul className="space-y-2 list-disc list-inside text-slate-600 text-sm">
              {exp.description.map((point, idx) => (
                <li key={idx} className="leading-relaxed">{point}</li>
              ))}
            </ul>
          </div>
        ))}
      </div>
    </div>
  </section>
);

const Projects = () => (
  <section id="projects" className="py-20 bg-slate-50 border-y border-slate-200 scroll-mt-20">
    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <SectionHeading 
        title="Featured Projects" 
        subtitle="Selected case studies highlighting architecture and impact." 
      />
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        {PROJECTS_DATA.map((project) => (
          <div key={project.id} className="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow group cursor-default">
            <div className="p-6">
              <span className="text-xs font-bold text-blue-600 uppercase tracking-wider mb-2 block">{project.category}</span>
              <h3 className="text-xl font-bold text-slate-900 mb-3 leading-snug group-hover:text-blue-600 transition-colors">{project.title}</h3>
              <p className="text-slate-600 text-sm mb-4 leading-relaxed line-clamp-3">{project.description}</p>
              <div className="mb-4">
                <h4 className="text-xs font-semibold text-slate-400 uppercase mb-1">Impact</h4>
                <p className="text-xs text-slate-700 italic">"{project.impact}"</p>
              </div>
              <div className="flex flex-wrap gap-2">
                {project.tech.map((t, idx) => (
                  <span key={idx} className="px-2 py-0.5 bg-slate-100 text-slate-600 text-[9px] font-bold rounded uppercase">
                    {t}
                  </span>
                ))}
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  </section>
);

const Contact = () => (
  <section id="contact" className="py-20 bg-white scroll-mt-20">
    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div className="bg-slate-900 rounded-[2rem] p-8 md:p-16 text-center text-white relative overflow-hidden shadow-2xl">
        <div className="relative z-10">
          <h2 className="text-3xl md:text-5xl font-bold mb-6">Let's Build or Support Something <span className="text-blue-400">Stable</span>.</h2>
          <p className="text-slate-400 mb-10 max-w-2xl mx-auto text-base md:text-lg leading-relaxed">
            I am currently available for full-time roles, contracts, and systems-focused consulting. 
            If you need a reliable engineer to scale your platform, I'd love to hear from you.
          </p>
          <div className="flex flex-col sm:flex-row justify-center gap-6">
            <a href="mailto:teslim@unibooks.com.ng" className="px-8 py-4 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all flex items-center justify-center shadow-lg shadow-blue-500/20">
              <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
              Email Me Directly
            </a>
            <a href="https://github.com" target="_blank" rel="noreferrer" className="px-8 py-4 bg-slate-800 text-white font-bold rounded-xl hover:bg-slate-700 transition-all flex items-center justify-center">
              <svg className="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.041-1.416-4.041-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
              </svg>
              GitHub Profile
            </a>
          </div>
        </div>
        {/* Glow Effects */}
        <div className="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-blue-500 rounded-full opacity-10 blur-3xl"></div>
        <div className="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-blue-600 rounded-full opacity-10 blur-3xl"></div>
      </div>
    </div>
  </section>
);

const Footer = () => (
  <footer className="py-12 bg-white border-t border-slate-100 mt-auto">
    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div className="flex flex-col md:flex-row justify-between items-center text-center md:text-left">
        <div className="mb-4 md:mb-0">
          <p className="text-slate-500 text-xs md:text-sm font-medium">© {new Date().getFullYear()} Teslim Bolaji. Designed for Stability.</p>
        </div>
        <div className="flex flex-wrap justify-center space-x-6 text-xs md:text-sm text-slate-500">
          <span className="font-bold text-slate-900">teslim.unibooks.com.ng</span>
        </div>
      </div>
    </div>
  </footer>
);

export default function App() {
  const [view, setView] = useState<ViewState>('home');

  return (
    <div className="min-h-screen flex flex-col bg-[#f8fafc]">
      <Navbar setView={setView} currentView={view} />
      <main className="flex-grow">
        {view === 'home' ? (
          <HomeView setView={setView} />
        ) : (
          <ServicesPage setView={setView} />
        )}
        
        {/* Experience and Projects are anchored sections on the home view */}
        {view === 'home' && (
          <>
            <Experience />
            <Projects />
          </>
        )}
        <Contact />
      </main>
      <Footer />
    </div>
  );
}
