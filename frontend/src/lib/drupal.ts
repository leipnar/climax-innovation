const DRUPAL_API_URL = 'https://climaxinnovation.com/cms/web/jsonapi';

interface DrupalService {
  id: string;
  type: string;
  attributes: {
    title: string;
    body: {
      value: string;
      processed: string;
    };
    field_icon: string;
    path: {
      alias: string | null;
    };
  };
}

interface DrupalTestimonial {
  id: string;
  type: string;
  attributes: {
    title: string;
    body: {
      value: string;
      processed: string;
    };
    field_client_name: string;
    field_role: string;
    field_company: string;
  };
}

export interface Service {
  id: string;
  title: string;
  description: string;
  icon: string;
}

export interface Testimonial {
  id: string;
  name: string;
  role: string;
  company: string;
  content: string;
}

export async function getServices(): Promise<Service[]> {
  try {
    const response = await fetch(
      `${DRUPAL_API_URL}/node/service?sort=drupal_internal__nid&fields[node--service]=title,body,field_icon`
    );
    
    if (!response.ok) {
      throw new Error(`Failed to fetch services: ${response.status}`);
    }
    
    const data = await response.json();
    
    return data.data.map((item: DrupalService) => ({
      id: item.id,
      title: item.attributes.title,
      description: item.attributes.body?.processed || item.attributes.body?.value || '',
      icon: item.attributes.field_icon || 'data',
    }));
  } catch (error) {
    console.error('Error fetching services:', error);
    return getFallbackServices();
  }
}

export async function getTestimonials(): Promise<Testimonial[]> {
  try {
    const response = await fetch(
      `${DRUPAL_API_URL}/node/testimonial?sort=drupal_internal__nid&fields[node--testimonial]=title,body,field_client_name,field_role,field_company`
    );
    
    if (!response.ok) {
      throw new Error(`Failed to fetch testimonials: ${response.status}`);
    }
    
    const data = await response.json();
    
    return data.data.map((item: DrupalTestimonial) => ({
      id: item.id,
      name: item.attributes.field_client_name || 'Anonymous',
      role: item.attributes.field_role || '',
      company: item.attributes.field_company || '',
      content: item.attributes.body?.processed || item.attributes.body?.value || '',
    }));
  } catch (error) {
    console.error('Error fetching testimonials:', error);
    return getFallbackTestimonials();
  }
}

function getFallbackServices(): Service[] {
  return [
    { id: '1', title: 'Real-Time Monitoring', description: 'Track equipment, materials, and personnel across your construction sites with IoT-enabled sensors.', icon: 'monitoring' },
    { id: '2', title: 'Safety & Risk Management', description: 'Proactive safety monitoring with automated alerts and comprehensive compliance tracking.', icon: 'safety' },
    { id: '3', title: 'Resource Management', description: 'Optimize labor, equipment, and material allocation for maximum efficiency.', icon: 'resource' },
    { id: '4', title: 'AI Assistant', description: 'Intelligent project management with AI-powered insights and recommendations.', icon: 'ai' },
    { id: '5', title: 'Sustainability Initiatives', description: 'Track and reduce environmental impact with smart resource management.', icon: 'sustainability' },
    { id: '6', title: 'Data Analytics', description: 'Transform raw data into actionable insights for better decision-making.', icon: 'data' },
    { id: '7', title: 'Preventive Maintenance', description: 'Predict and prevent equipment failures before they impact your project.', icon: 'maintenance' },
    { id: '8', title: 'Supply Chain Management', description: 'Streamline procurement and logistics for on-time material delivery.', icon: 'supply' },
  ];
}

function getFallbackTestimonials(): Testimonial[] {
  return [
    { id: '1', name: 'Michael Chen', role: 'Project Director', company: 'BuildTech Corp', content: 'Climax Innovation transformed how we manage our construction sites. The real-time monitoring has reduced our project delays by 40%.' },
    { id: '2', name: 'Sarah Williams', role: 'Safety Manager', company: 'SafeBuild Inc', content: 'The safety management features are exceptional. We have seen a significant reduction in workplace incidents since implementation.' },
    { id: '3', name: 'David Rodriguez', role: 'Operations Lead', company: 'ConstructPlus', content: 'The AI assistant provides insights that would have taken us weeks to compile manually. Game-changing for project management.' },
  ];
}
