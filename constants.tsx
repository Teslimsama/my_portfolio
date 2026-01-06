
import React from 'react';
import { ExperienceItem, ProjectItem, SkillCategory } from './types';

export const EXPERIENCE_DATA: ExperienceItem[] = [
  {
    id: '1',
    role: 'Backend & Application Support Engineer',
    company: 'Topupmate',
    period: 'Present',
    description: [
      'Managing backend architecture for high-volume VTU and utility payment systems.',
      'Ensuring platform stability through rigorous debugging and SLA-driven incident resolution.',
      'Optimizing database queries and background job processing to handle scale.',
      'Integrating 3rd party messaging and payment gateways like Termii and Paystack.'
    ],
    type: 'Full-time'
  },
  {
    id: '2',
    role: 'Technical Support Specialist & Developer',
    company: 'Panthers Insights',
    period: 'Previous',
    description: [
      'Resolved complex technical issues for enterprise clients using a mix of support and development.',
      'Maintained legacy codebases, ensuring continuity and performance upgrades.',
      'Collaborated on system integrations and database maintenance across MS SQL and MySQL.',
      'Developed custom reporting tools for stakeholders using PHP and Blade.'
    ],
    type: 'Full-time'
  },
  {
    id: '3',
    role: 'Freelance Backend Engineer',
    company: 'Self-Employed',
    period: 'Ongoing',
    description: [
      'Architecting custom ERP solutions for educational and business organizations.',
      'Developing RESTful APIs with Laravel for multi-tenant web platforms.',
      'Consulting on database design and system optimization for small-to-medium enterprises.',
      'Managing infrastructure deployments on Linux-based environments.'
    ],
    type: 'Contract'
  }
];

export const PROJECTS_DATA: ProjectItem[] = [
  {
    id: 'p1',
    title: 'School ERP / Student Management System',
    category: 'Enterprise Solution',
    description: 'A comprehensive system managing student records, grading, and administrative workflows.',
    tech: ['PHP', 'Laravel', 'MySQL', 'Blade', 'Redis'],
    impact: 'Streamlined administrative tasks by 40% and improved data accuracy across multiple campuses.'
  },
  {
    id: 'p2',
    title: 'CRM & Bulk Messaging Platform',
    category: 'System Integration',
    description: 'A multi-channel communication platform integrating SendGrid and Termii for automated alerts.',
    tech: ['Laravel', 'PostgreSQL', 'RESTful APIs', 'Background Jobs'],
    impact: 'Enabled reliable delivery of 100k+ monthly messages with real-time status tracking.'
  },
  {
    id: 'p3',
    title: 'Multi-Tenant SaaS Infrastructure',
    category: 'Infrastructure',
    description: 'Architecture for a platform supporting multiple distinct organizations under a single backend.',
    tech: ['PHP', 'MS SQL', 'Docker', 'Linux', 'Log Analysis'],
    impact: 'Reduced deployment overhead for new clients from days to minutes through automated provisioning logic.'
  }
];

export const SKILLS_DATA: SkillCategory[] = [
  {
    title: 'Backend Development',
    skills: ['PHP (Laravel)', 'RESTful APIs', 'MVC Architecture', 'OOP', 'Background Jobs', 'Queues']
  },
  {
    title: 'Databases',
    skills: ['MySQL', 'PostgreSQL', 'MS SQL', 'Query Optimization', 'Database Design']
  },
  {
    title: 'Systems & Tools',
    skills: ['Git/GitHub', 'Docker', 'Postman', 'Linux Basics', 'Redis', 'SendGrid', 'Termii']
  },
  {
    title: 'Application Support',
    skills: ['Incident Resolution', 'SLA Management', 'Log Analysis', 'Debugging', 'Post-launch Support']
  }
];
