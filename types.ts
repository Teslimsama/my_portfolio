
export interface ExperienceItem {
  id: string;
  role: string;
  company: string;
  period: string;
  description: string[];
  type: 'Full-time' | 'Contract' | 'Freelance';
}

export interface ProjectItem {
  id: string;
  title: string;
  category: string;
  description: string;
  tech: string[];
  impact: string;
}

export interface SkillCategory {
  title: string;
  skills: string[];
}
